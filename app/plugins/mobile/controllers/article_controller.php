<?php
App::import("vendor", array("model/section", "model/board", "model/threads", "inc/ubb"));
class ArticleController extends MobileAppController {

    private $_board;

    public function beforeFilter(){
        parent::beforeFilter();
        if(!isset($this->params['name'])){
            $this->error(ECode::$BOARD_NONE);
        }
        try{
            $boardName = $this->params['name'];
            $this->_board = Board::getInstance($boardName);
        }catch(BoardNullException $e){
            $this->error(ECode::$BOARD_UNKNOW);
        }
        if(!$this->_board->hasReadPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPERM);
        }
        $this->_board->setOnBoard();
    }

    public function index(){
        $this->notice = "版面-{$this->_board->DESC}({$this->_board->NAME})";

        App::import('Sanitize');
        App::import('vendor', array("inc/pagination", "inc/astro"));
        try{
            $gid = $this->params['gid'];
            $threads = Threads::getInstance($gid, $this->_board);
        }catch(ThreadsNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
        $pagination = new Pagination($threads, Configure::read("pagination.article"));
        $articles = $pagination->getPage($p);

        $u = User::getInstance();
        $bm = $u->isBM($this->_board) || $u->isAdmin();
        $info = array();
        foreach($articles as $v){
            $content = $v->getHtml();
            preg_match("|※ 修改:·([\S]+) .*?FROM:[\s]*([0-9a-zA-Z.:*]+)|", $content, $m);
            preg_match("|※ 来源:.*FROM:[\s]*([0-9a-zA-Z.:*]+)|", $content, $f);
            $m = empty($m)?"":"<br />修改:{$m[1]} FROM {$m[2]}";
            $f = empty($f)?"":"<br />FROM {$f[1]}";
            $s = (($pos = strpos($content, "<br/><br/>")) === false)?0:$pos + 10;
            $e = (($pos = strpos($content, "<br/>--<br/>")) === false)?strlen($content):$pos + 7;
            $content = substr($content, $s, $e - $s) . $m . $f;
            if(Configure::read("ubb.parse")){
                $content = XUBB::parse($content);
            }
            $info[] = array(
                "id" => $v->ID,
                "op" => ($v->OWNER == $u->userid || $bm)?1:0,
                "time" => date("Y-m-d H:i:s", $v->POSTTIME),
                "pos" => $v->getPos(),
                "poster" => $v->OWNER,
                "content" => $content,
                "subject" => $v->isSubject()
            );
        }
        $this->set("bName", $this->_board->NAME);
        $this->set("anony", $this->_board->isAnony());
        $this->set("canPost", $this->_board->hasPostPerm($u));
        $this->set("info", $info);
        $this->set("gid", $threads->GROUPID);
        $this->set("title", Sanitize::html($threads->TITLE));
        $this->set("curPage", $pagination->getCurPage());
        $this->set("totalPage", $pagination->getTotalPage());
        //for the quick reply, raw encode the space
        $this->set("reid", $threads->ID);
        if(!strncmp($threads->TITLE, "Re: ", 4))
            $reTitle = $threads->TITLE;
        else
            $reTitle = "Re: " . $threads->TITLE;
        $this->set("reTitle", rawurlencode($reTitle));
    }

    public function single(){
        $this->notice = "版面-{$this->_board->DESC}({$this->_board->NAME})";

        App::import('Sanitize');
        try{
            $gid = $this->params['gid'];
            $article = Article::getInstance($gid, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }

        $u = User::getInstance();
        $bm = $u->isBM($this->_board) || $u->isAdmin();
        $info = array();
        $content = $article->getHtml();
        preg_match("|※ 修改:·([\S]+) .*?FROM:[\s]*([0-9a-zA-Z.:*]+)|", $content, $m);
        preg_match("|※ 来源:.*FROM:[\s]*([0-9a-zA-Z.:*]+)|", $content, $f);
        $m = empty($m)?"":"<br />修改:{$m[1]} FROM {$m[2]}";
        $f = empty($f)?"":"<br />FROM {$f[1]}";
        $s = (($pos = strpos($content, "<br/><br/>")) === false)?0:$pos + 10;
        $e = (($pos = strpos($content, "<br/>--<br/>")) === false)?strlen($content):$pos + 7;
        $content = substr($content, $s, $e - $s) . $m . $f;
        if(Configure::read("ubb.parse")){
            $content = XUBB::parse($content);
        }
        $info = array(
            "aid" => $article->ID,
            "gid" => $article->GROUPID,
            "op" => ($article->OWNER == $u->userid || $bm)?1:0,
            "time" => date("Y-m-d H:i:s", $article->POSTTIME),
            "poster" => $article->OWNER,
            "content" => $content,
            "subject" => $article->isSubject(),
            "pre" => ($a = $article->pre())?$a->ID:false,
            "next" => ($a = $article->next())?$a->ID:false,
            "tPre" => ($a = $article->tPre())?$a->ID:false,
            "tNext" =>($a = $article->tNext())?$a->ID:false
        );
        $this->set("bName", $this->_board->NAME);
        $this->set("canPost", $this->_board->hasPostPerm($u));
        $this->set("title", Sanitize::html($article->TITLE));
        $this->set($info);
    }

    public function post(){
        if($this->_board->isReadOnly()){
            $this->error(ECode::$BOARD_READONLY);
        }
        if(!$this->_board->hasPostPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPOST);
        }
        if(isset($this->params['gid'])){
            $reID = (int) $this->params['gid'];
            if($this->_board->isNoReply())
                $this->error(ECode::$BOARD_NOREPLY);
            try{
                $article = Article::getInstance($reID, $this->_board);
            }catch(ArticleNullException $e){
                $this->error(ECode::$ARTICLE_NOREID);
            }
            if($article->isNoRe())
                $this->error(ECode::$ARTICLE_NOREPLY);
        }else{
            if($this->_board->isTmplPost())
                $this->error(ECode::$TMPL_ERROR);
            $reID = 0;
        }
        $single = (isset($this->params['url']['s']) || isset($this->params['form']['s']));

        if($this->RequestHandler->isPost()){
            if(!isset($this->params['form']['subject']))
                $this->error(ECode::$POST_NOSUB);
            if(!isset($this->params['form']['content']))
                $this->error(ECode::$POST_NOCON);
            $subject = trim($this->params['form']['subject']);
            $content = trim($this->params['form']['content']);
            if($this->encoding != Configure::read("App.encoding")){
                $subject = iconv($this->encoding, Configure::read("App.encoding")."//IGNORE", $subject);
                $content = iconv($this->encoding, Configure::read("App.encoding")."//IGNORE", $content);
            }
            $subject = rawurldecode($subject);
            $sig = User::getInstance()->signature;
            $email = 0;$anony = null;
            if(isset($this->params['form']['email']))
                $email = 1;
            if(isset($this->params['form']['anony']))
                $anony = 1;
            try{
                Article::post($this->_board, $subject, $content, $sig, $reID, $email, $anony);
            }catch(ArticlePostException $e){
                $this->error($e->getMessage());
            }
            $this->redirect($this->_mbase . "/board/" . $this->_board->NAME . ($single?"/0":"") . "?m=" . ECode::$POST_OK);
        }else{
            $reTitle = $reContent = "";
            if($reID != 0){
                $this->notice = "{$this->_board->DESC}-回复";
                $reContent = "\n".$article->getRef();
                //remove ref ubb tag
                $reContent = XUBB::remove($reContent);
                if(!strncmp($article->TITLE, "Re: ", 4))
                    $reTitle = $article->TITLE;
                else
                    $reTitle = "Re: " . $article->TITLE;
            }else{
                $this->notice = "{$this->_board->DESC}-发表";
            }
        }
        $this->set("single", $single);
        $this->set("bName", $this->_board->NAME);
        $this->set("email", true);
        $this->set("anony", $this->_board->isAnony());
        $this->set("title", (string)$reTitle);
        $this->set("content", (string)$reContent);
    }

    public function edit(){
        if($this->_board->isReadOnly()){
            $this->error(ECode::$BOARD_READONLY);
        }
        if(!$this->_board->hasPostPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPOST);
        }
        if(!isset($this->params['gid']))
            $this->error(ECode::$ARTICLE_NONE);
        $id = (int)$this->params['gid'];
        try{
            $article = Article::getInstance($id, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        if(!$article->hasEditPerm(User::getInstance()))
            $this->error(ECode::$ARTICLE_NOEDIT);
        $single = (isset($this->params['url']['s']) || isset($this->params['form']['s']));
        if($this->RequestHandler->isPost()){
            $subject = trim($this->params['form']['subject']);
            $content = trim($this->params['form']['content']);
            if($this->encoding != Configure::read("App.encoding")){
                $subject = iconv($this->encoding, Configure::read("App.encoding")."//IGNORE", $subject);
                $content = iconv($this->encoding, Configure::read("App.encoding")."//IGNORE", $content);
            }
            $subject = rawurldecode($subject);
            if(!$article->update($subject, $content))
                $this->error(ECode::$ARTICLE_EDITERROR);
            $this->redirect($this->_mbase . "/board/" . $this->_board->NAME . ($single?"/0":"") . "?m=" . ECode::$ARTICLE_EDITOK);
        }else{
            $this->notice = "{$this->_board->DESC}-编辑";
            $title = $article->TITLE;
            $content = $article->getContent();
        }

        $this->set("bName", $this->_board->NAME);
        $this->set("email", false);
        $this->set("anony", false);
        $this->set("title", $title);
        $this->set("content", $content);
        $this->set("single", $single);
        $this->autoRender = false;
        $this->render("post");
    }

    public function delete(){
        $u = User::getInstance();
        if(isset($this->params['gid'])){
            try{
                $a = Article::getInstance(intval($this->params['gid']), $this->_board);
                if(!$a->hasEditPerm($u))
                    $this->error(ECode::$ARTICLE_NODEL);
                if(!$a->delete())
                    $this->error(ECode::$ARTICLE_NODEL);
            }catch(ArticleNullException $e){
                $this->error(ECode::$ARTICLE_NONE);
            }
        }
        $this->redirect($this->_mbase . "/board/" . $this->_board->NAME . "?m=" . ECode::$ARTICLE_DELOK);
    }

    public function focus(){
        $focusBan = Configure::read("focus.ban");
        if(in_array($this->_board->NAME, $focusBan)){
            $this->error(ECode::$ARTICLE_REERROR);
        }
        $id = $this->params['gid'];
        try{
            $article = Article::getInstance($id, $this->_board);
            if(!$article->isSubject() || !$article->postFocus())
                $this->error(ECode::$ARTICLE_REERROR);

        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $this->redirect($this->_mbase . "/article/" . $this->_board->NAME . "/{$id}?m=" . ECode::$ARTICLE_REOK);
    }
}
?>
