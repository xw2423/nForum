<?php
/**
 * Article controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/section", "model/board", "model/threads", "inc/ubb"));
class ArticleController extends AppController {
    
    private $_threads;
    private $_board;

    public function beforeFilter(){
        parent::beforeFilter();
        $this->_init();
    }

    public function index(){
        $this->css[] = "ubb.css";
        $this->css[] = "article.css";
        $this->css[] = "ansi.css";
        $this->js[] = "forum.xubb.js";
        $this->js[] = "forum.article.js";
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"阅读文章");

        $this->cache(false);
        App::import('Sanitize');
        App::import('vendor', array("inc/pagination", "inc/astro"));
        try{
            $gid = $this->params['gid'];
            $this->_threads = Threads::getInstance($gid, $this->_board);
        }catch(ThreadsNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
        $pagination = new Pagination($this->_threads, Configure::read("pagination.article"));
        $articles = $pagination->getPage($p);

        $u = User::getInstance();
        $bm = $u->isBM($this->_board) || $u->isAdmin();
        $info = array();
        $curTime = strtotime(date("Y-m-d", time()));
        foreach($articles as $v){
            try{
                $own = User::getInstance($v->OWNER); 
                $astro = Astro::getAstro($own->birthmonth, $own->birthday);

                if($own->getCustom("userdefine0", 29) == 0){
                    $hide = true;
                    $gender = -1;
                }else{
                    $hide = false;
                    $gender = ($own->gender == "77")?0:1;
                }
                $user = array(
                    "id" => $own->userid,
                    "name" => Sanitize::html($own->username),
                    "gender" => $gender,
                    "furl" => Sanitize::html($own->getFace()),
                    "width" => ($own->userface_width === 0)?"":$own->userface_width,
                    "height" => ($own->userface_height === 0)?"":$own->userface_height,
                    "post" => $own->numposts,
                    "astro" => $astro['name'],
                    "online" => $own->isOnline(),
                    "level" => $own->getLevel(),
                    "time" => date(($curTime > $own->lastlogin)?"Y-m-d":"H:i:s", $own->lastlogin),
                    "first" => date("Y-m-d", $own->firstlogin),
                    "hide" => $hide
                );
            }catch(UserNullException $e){
                $user = false;
            }

            $this->title = Sanitize::html($this->_threads->TITLE);
            $content = $v->getHtml(true);
            //hard to match all the format of ip
            //$pattern = '/<font class="f[0-9]+">※( |&nbsp;)来源:·.+?\[FROM:( |&nbsp;)[0-9a-zA-Z.:*]+\]<\/font><font class="f000">( +<br \/>)+ +<\/font>/';
            //preg_match($pattern, $content, $match);
            //$content = preg_replace($pattern, "", $content);
            if(Configure::read("ubb.parse")){
                //remove ubb of nickname in first and title second line
                $content = preg_replace("'^(.*?<br \/>.*?<br \/>)'e", "XUBB::remove('\\1')", $content);
                $content = XUBB::parse($content);
            }
            $info[] = array(
                "id" => $v->ID,
                "owner" => $user,
                "op" => ($v->OWNER == $u->userid || $bm)?1:0,
                "pos" => $v->getPos(),
                "poster" => $v->OWNER,
                "content" => $content,
                "subject" => $v->isSubject()
                //"from" => isset($match[0])?preg_replace("/<br \/>/", "", $match[count($match)-1]):""
            );
        }
        $link = "?p=%page%";
        $pageBar = $pagination->getPageBar($p, $link);
        $this->set("bName", $this->_board->NAME);
        $this->set("anony", $this->_board->isAnony());
        $this->set("tmpl", $this->_board->isTmplPost());
        $this->set("info", $info);
        $this->set("pageBar", $pageBar);
        $this->set("title", Sanitize::html($this->_threads->TITLE));
        $this->set("totalNum", $this->_threads->getTotalNum());
        $this->set("curPage", $pagination->getCurPage());
        $this->set("totalPage", $pagination->getTotalPage());
        //for the quick reply, raw encode the space
        $this->set("reid", $this->_threads->ID);
        if(!strncmp($this->_threads->TITLE, "Re: ", 4))
            $reTitle = $this->_threads->TITLE;
        else
            $reTitle = "Re: " . $this->_threads->TITLE;
        $this->set("reTitle", rawurlencode($reTitle));
        //for default search day 
        $this->set("searchDay", Configure::read("search.day"));
        $this->set("searchDay", Configure::read("search.day"));
        $this->jsr[] = "var user_post=" . ($this->_board->hasPostPerm($u)?"true":"false") . ";";
    }

    public function post(){
        $this->_postInit();
        if($this->RequestHandler->isPost()){
            if(isset($this->params['form']['reid'])){
                $reID = $this->params['form']['reid'];
            }else{
                if($this->_board->isTmplPost()){
                    $this->redirect("/article/" . $this->_board->NAME . "/tmpl");
                }
                $reID = 0;
            }
            if(!isset($this->params['form']['subject']))
                $this->error(ECode::$POST_NOSUB);
            if(!isset($this->params['form']['content']))
                $this->error(ECode::$POST_NOCON);
            $subject = rawurldecode(trim($this->params['form']['subject']));
            if(strlen($subject) > 60)
                $subject = nforum_fix_gbk(substr($subject,0,60));
            $content = trim($this->params['form']['content']);
            $sig = User::getInstance()->signature;
            $email = 0;$anony = null;
            if(isset($this->params['form']['signature']))
                $sig = intval($this->params['form']['signature']);
            if(isset($this->params['form']['email']))
                $email = 1;
            if(isset($this->params['form']['anony']))
                $anony = 1;
            try{
                $id = Article::post($this->_board, $subject, $content, $sig, $reID, $email, $anony);
                $gid = Article::getInstance($id, $this->_board);
                $gid = $gid->GROUPID;
            }catch(ArticlePostException $e){
                $this->error($e->getMessage());
            }
            $this->waitDirect(
                array(
                    "text" => $this->_board->DESC, 
                    "url" => "/board/" . $this->_board->NAME
                ), ECode::$POST_OK, 
                array(array("text" => str_replace('Re: ', '', $subject), "url" => '/article/' .  $this->_board->NAME . '/' . $gid)
                    ,array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"))
                ));
        }

        $this->js[] = "forum.xubb.js";
        $this->js[] = "forum.post.js";
        $this->css[] = "post.css";
        $this->css[] = "ubb.css";
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"发表文章");

        $reTitle = $reContent = "";
        if(isset($this->params['id'])){
            $reID = $this->params['id'];
            try{
                $article = Article::getInstance($reID, $this->_board);
            }catch(ArticleNullException $e){
                $this->error(ECode::$ARTICLE_NONE);
            }
            $reContent = "\n".$article->getRef();
            //remove ref ubb tag
            $reContent = XUBB::remove($reContent);
            if(!strncmp($article->TITLE, "Re: ", 4))
                $reTitle = $article->TITLE;
            else
                $reTitle = "Re: " . $article->TITLE;
        }else{
            if($this->_board->isTmplPost()){
                $this->redirect("/article/" . $this->_board->NAME . "/tmpl");
            }
            $reID = 0;
        }
        $u = User::getInstance();
        $sigOption = array();
        foreach(range(0, $u->signum) as $v){
            if($v == 0)
                $sigOption["$v"] = "不使用签名档";
            else
                $sigOption["$v"] = "使用第{$v}个";
        }
        $sigOption["-1"] = "使用随机签名档";
        $this->set("bName", $this->_board->NAME);
        $this->set("anony", $this->_board->isAnony());
        $this->set("isAtt", $this->_board->isAttach());
        $this->set("reTitle", $reTitle);
        $this->set("reContent", $reContent);
        $this->set("sigOption", $sigOption);
        $this->set("sigNow", $u->signature);
        $this->set("reID", $reID);
    }

    public function delete(){
        $u = User::getInstance();
        if(isset($this->params['id'])){
            try{
                $a = Article::getInstance(intval($this->params['id']), $this->_board);
                if(!$a->hasEditPerm($u))
                    $this->error(ECode::$ARTICLE_NODEL);
                if(!$a->delete())
                    $this->error(ECode::$ARTICLE_NODEL);
            }catch(ArticleNullException $e){
                $this->error(ECode::$ARTICLE_NONE);
            }
        }
        $this->waitDirect(
            array(
                "text" => $this->_board->DESC, 
                "url" => "/board/" . $this->_board->NAME
            ),ECode::$ARTICLE_DELOK,
            array(
                array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"))
            ));

    }

    public function edit(){
        $this->_editInit();
        $id = $this->params['id'];
        if($this->RequestHandler->isPost()){
            if(!isset($this->params['form']['subject']))
                $this->error(ECode::$POST_NOSUB);
            if(!isset($this->params['form']['content']))
                $this->error(ECode::$POST_NOCON);
            $subject = trim($this->params['form']['subject']);
            if(strlen($subject) > 60)
                $subject = nforum_fix_gbk(substr($subject,0,60));
            $content = trim($this->params['form']['content']);
            $article = Article::getInstance($id, $this->_board);
            if(!$article->update($subject, $content))
                $this->error(ECode::$ARTICLE_EDITERROR);
            $this->waitDirect(
                array(
                    "text" => $this->_board->DESC, 
                    "url" => "/board/" . $this->_board->NAME
                ),ECode::$ARTICLE_EDITOK,
                array(array("text" => $subject, "url" => '/article/' .  $this->_board->NAME . '/' . $article->GROUPID)
                    ,array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"))
                ));
        }

        $this->js[] = "forum.xubb.js";
        $this->js[] = "forum.post.js";
        $this->css[] = "post.css";
        $this->css[] = "ubb.css";
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"编辑文章");

        $article = Article::getInstance($id, $this->_board);
        $title = $article->TITLE;
        $content = $article->getContent();
        $this->set("bName", $this->_board->NAME);
        $this->set("isAtt", $this->_board->isAttach());
        $this->set("title", $title);
        $this->set("content", $content);
        $this->set("eid", $id);
    }

    public function focus(){
        $focusBan = Configure::read("focus.ban");
        if(in_array($this->_board->NAME, $focusBan)){
            $this->error(ECode::$ARTICLE_REERROR);
        }
        $id = $this->params['id'];
        try{
            $article = Article::getInstance($id, $this->_board);
            if(!$article->isSubject() || !$article->postFocus())
                $this->error(ECode::$ARTICLE_REERROR);

        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $this->waitDirect(
            array(
                "text" => $this->_board->DESC, 
                "url" => "/board/" . $this->_board->NAME
            ),ECode::$ARTICLE_REOK,
            array(
                array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"))
            ));
        
    }

    public function preview(){
        $this->css[] = "article.css";
        $this->css[] = "ansi.css";
        $this->notice[] = array("url"=>"", "text"=>"发文预览");
        App::import('Sanitize');
        if(!isset($this->params['form']['title']) || !isset($this->params['form']['content'])){
            $this->error();
        }

        $title = Sanitize::html($this->params['form']['title']);
        $content = preg_replace("/\n/", "<br />", Sanitize::html($this->params['form']['content']));
        if(Configure::read("ubb.parse"))
            $content = XUBB::parse($content);
        $this->set("title", $title);
        $this->set("content", $content);
    }

    public function tmpl(){
        $this->_postInit();
        App::import("vendor", "model/template");
        App::import('Sanitize');
        $this->css[] = "post.css";
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"模版发文");

        if(isset($this->params['id'])){
            $id = trim($this->params['id']);
            try{
                $t = Template::getInstance($id, $this->_board);
            }catch(TemplateNullException $e){
                $this->error(ECode::$TMPL_ERROR);
            }
            if($this->RequestHandler->isPost() ){
                $val = $this->params['form']['q'];
                $pre = $t->getPreview($val);
                $title = $pre[0];
                $preview = $pre[1];
                $content = $pre[2];
                if($this->params['form']['pre'] == "0"){
                    $u = User::getInstance();
                    try{
                        Article::post($this->_board, $title, $content, $u->signature, 0, 0);
                    }catch(ArticlePostException $e){
                        $this->error($e->getMessage());
                    }
                    $this->waitDirect(
                        array(
                            "text" => $this->_board->DESC, 
                            "url" => "/board/" . $this->_board->NAME
                        ), ECode::$POST_OK, 
                        array(
                            array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"))
                        ));
                }else{
                    if(Configure::read("ubb.parse"))
                        $content = XUBB::parse($content);
                    $this->set("title", $title);
                    $this->set("content", $preview);
                    array_pop($this->css);
                    $this->css[] = "article.css";
                    $this->css[] = "ansi.css";
                    $this->render("preview");
                }
            }else{
                $info = array();
                try{
                    foreach(range(0, $t->CONT_NUM - 1) as $i){
                        $q = $t->getQ($i);
                        $info[$i] = array("text" => Sanitize::html($q['TEXT']), "len"=>$q['LENGTH']);
                    }
                }catch(TemplateQNullException $e){
                    $this->error();
                }
                $this->set("info", $info);
                $this->set("num", $t->NUM);
                $this->set("tmplTitle", Sanitize::html($t->TITLE));
                $this->set("title", $t->TITLE_TMPL);
                $this->render("tmpl_que");
            }
        }else{
            try{
                $page = new Pagination(Template::getTemplates($this->_board));
            }catch(TemplateNullException $e){
                $this->error(ECode::$TMPL_ERROR);
            }
            $info = $page->getPage(1);

            foreach($info as &$v){
                $v = array("name" => Sanitize::html($v->TITLE), "num" => $v->CONT_NUM);
            }
            $this->set("info", $info);
            $this->set("bName", $this->_board->NAME);
        }
    }

    private function _init(){
        if(!isset($this->params['name'])){
            $this->error(ECode::$BOARD_NONE);
        }

        try{
            $boardName = $this->params['name'];
            if(preg_match("/^\d+$/", $boardName))
                throw new BoardNullException();
            $this->_board = Board::getInstance($boardName);
        }catch(BoardNullException $e){
            $this->error(ECode::$BOARD_UNKNOW);
        }

        if(!$this->_board->hasReadPerm(User::getInstance())){
            if(!$this->ByrSession->isLogin)
                $this->requestLogin();
            $this->error(ECode::$BOARD_NOPERM);
        }
        $this->_board->setOnBoard();
        $this->ByrSession->Cookie->write("XWJOKE", "hoho", false);
    }

    private function _postInit(){
        if($this->_board->isReadOnly()){
            $this->error(ECode::$BOARD_READONLY);
        }
        if(!$this->_board->hasPostPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPOST);
        }
        if(isset($this->params['form']['reid']) || isset($this->params['url']['reid'])){
            @$reID = intval($this->params['url']['reid']);
            if($reID == "")
                $reID = intval($this->params['form']['reid']);
            if($reID == "0")
                return;
            if($this->_board->isNoReply())
                $this->error(ECode::$BOARD_NOREPLY);
            try{
                $reArticle = Article::getInstance($reID, $this->_board);
            }catch(ArticleNullException $e){
                $this->error(ECode::$ARTICLE_NOREID);
            }
            if($reArticle->isNoRe())
                $this->error(ECode::$ARTICLE_NOREPLY);
        }
    }

    private function _editInit(){
        if($this->_board->isReadOnly()){
            $this->error(ECode::$BOARD_READONLY);
        }
        if(!$this->_board->hasPostPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPOST);
        }
        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        $id = intval($this->params['id']);
        try{
            $article = Article::getInstance($id, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        $u = User::getInstance();
        if(!$article->hasEditPerm($u))
            $this->error(ECode::$ARTICLE_NOEDIT);
    }

    private function _getNotice(){
        $root = Configure::read("section.{$this->_board->SECNUM}");
        $this->notice[] = array("url"=>"/section/{$this->_board->SECNUM}", "text"=>$root[0]);
        $boards = array(); $tmp = $this->_board;
        while(!is_null($tmp = $tmp->getDir())){
            $boards[] = array("url"=>"/section/{$tmp->NAME}", "text"=>$tmp->DESC);
        }
        foreach($boards as $v)
            $this->notice[] = $v;
        $this->notice[] = array("url"=>"/board/{$this->_board->NAME}", "text"=>$this->_board->DESC);
    }
}
?>
