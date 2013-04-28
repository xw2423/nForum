<?php
/**
 * Article controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/section", "model/board", "model/threads", "inc/ubb"));
class ArticleController extends AppController {

    public $components = array("Cookie");

    private $_threads;
    private $_board;

    public function beforeFilter(){
        parent::beforeFilter();
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

        if(isset($this->params['url']['mode'])){
            $mode = (int)trim($this->params['url']['mode']);
            if(!$this->_board->setMode($mode))
                $this->error(ECode::$BOARD_NOPERM);
        }
        if(!$this->_board->hasReadPerm(User::getInstance())){
            if(!$this->ByrSession->isLogin)
                $this->requestLogin();
            $this->error(ECode::$BOARD_NOPERM);
        }
        $this->_board->setOnBoard();
        $this->ByrSession->Cookie->write("XWJOKE", "hoho", false);
    }

    public function index(){
        $this->cache(false);
        $this->css[] = "article.css";
        $this->js[] = "forum.share.js";
        $this->js[] = "forum.article.js";
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"阅读文章");

        App::import('Sanitize');
        App::import('vendor', array("inc/pagination", "inc/astro"));

        if(!isset($this->params['gid']) || $this->params['gid'] == '0')
            $this->error(ECode::$ARTICLE_NONE);
        try{
            $gid = $this->params['gid'];
            $this->_threads = Threads::getInstance($gid, $this->_board);
        }catch(ThreadsNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }

        //article jump
        if(isset($this->params['url']['s'])){
            $article = $this->_threads->getArticleById(intval($this->params['url']['s']));
            if(null !== $article){
                $pos = $article->getPos();
                $page = ceil(($pos + 1) / Configure::read("pagination.article"));
                $this->redirect("/article/{$this->_board->NAME}/{$gid}?p={$page}#a{$pos}");
            }
            $this->redirect("/article/{$this->_board->NAME}/{$gid}");
        }

        //filter author
        $auF = $au = false;
        if(isset($this->params['url']['au'])){
            $tmp = $this->_threads->getRecord(1, $this->_threads->getTotalNum());
            $auF = array();$au = trim($this->params['url']['au']);
            foreach($tmp as $v){
                if($v->OWNER == $au)
                    $auF[] = $v;
            }
            $auF = new ArrayPageableAdapter($auF);
        }

        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
        $pagination = new Pagination(false !== $au?$auF:$this->_threads, Configure::read("pagination.article"));
        $articles = $pagination->getPage($p);

        $u = User::getInstance();
        if($bm = $u->isBM($this->_board) || $u->isAdmin())
            $this->js[] = "forum.manage.js";
        $info = array();
        $curTime = strtotime(date("Y-m-d", time()));
        $isUbb = Configure::read("ubb.parse");
        $isSyn = Configure::read("ubb.syntax");
        $hasSyn = false;
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

            $content = $v->getHtml(true);
            //hard to match all the format of ip
            //$pattern = '/<font class="f[0-9]+">※( |&nbsp;)来源:·.+?\[FROM:( |&nbsp;)[0-9a-zA-Z.:*]+\]<\/font><font class="f000">( +<br \/>)+ +<\/font>/';
            //preg_match($pattern, $content, $match);
            //$content = preg_replace($pattern, "", $content);
            if($isUbb){
                //remove ubb of nickname in first and title second line
                preg_match("'^(.*?<br \/>.*?<br \/>)'", $content, $res);
                $content = preg_replace("'(^.*?<br \/>.*?<br \/>)'", '', $content);
                $content = XUBB::remove($res[1]) . $content;
                $content = XUBB::parse($content);

                //check syntax
                if(!empty($isSyn) && preg_match("/<pre class=\"brush:/", $content))
                    $hasSyn = true;

                //parse vote
                if($v->OWNER === 'deliver' && in_array('vote', Configure::read('plugins.install'))){
                    $vid = array();
                    if(preg_match("'\[vote=(\d+)\]\[/vote\]'", $content, $vid)){
                        $content = preg_replace("'\[vote=\d+\]\[/vote\]'", '', $content);
                        App::import("vendor", array("inc/db", "vote.vote"));
                        $vid = $vid[1];
                        try{
                            $vote = new Vote($vid);
                            if(!$vote->isDeleted()){
                                $this->css['plugin']['vote'][] = "vote.css";
                                $this->js['plugin']['vote'][] = "vote.js";
                                $myres = $vote->getResult($u->userid);
                                $voted = false;
                                if($myres !== false){
                                    $voted = true;
                                    $myres['time'] = date("Y-m-d H:i:s", $myres['time']);
                                    $this->set("myres", $myres);
                                }
                                $vinfo = array(
                                    "vid" =>$vote->vid,
                                    "title" =>Sanitize::html($vote->subject),
                                    "desc" =>nl2br(Sanitize::html($vote->desc)),
                                    "start" =>date("Y-m-d H:i:s", $vote->start),
                                    "end" =>date("Y-m-d", $vote->end),
                                    "num" =>$vote->num,
                                    "type" =>$vote->type,
                                    "limit" =>$vote->limit,
                                    "aid" =>$vote->aid,
                                    "isEnd" =>$vote->isEnd(),
                                    "isDel" =>$vote->isDeleted(),
                                    "voted" =>$voted,
                                    "uid" =>$vote->uid
                                );
                                $item = $vote->items;
                                foreach($item as $kk=>$vv){
                                    $item[$kk]["label"] = Sanitize::html($vv["label"]);
                                    $item[$kk]["percent"] = ($vote->total === 0)?0:round(intval($vv['num'])*100/$vote->total);
                                    $item[$kk]["on"] = ($myres !== false) && in_array($vv['viid'], $myres['items']);
                                }
                                $this->set("vinfo", $vinfo);
                                $this->set("vitems", $item);
                                $this->set("result_voted", $vote->result_voted);
                                $this->set("no_result", !($u->userid === $vote->uid || $u->isAdmin()) && $vote->result_voted && !$voted);
                            }
                        }catch(VoteNullException $e){}
                    }
                }
            }
            $info[] = array(
                "id" => $v->ID,
                "owner" => $user,
                "op" => ($v->OWNER == $u->userid || $bm)?1:0,
                "pos" => $v->getPos(),
                "poster" => $v->OWNER,
                "content" => $content,
                "subject" => $v->isSubject(),
                //"from" => isset($match[0])?preg_replace("/<br \/>/", "", $match[count($match)-1]):""
                'g' => $v->isG(),
                'm' => $v->isM(),
                'l' => $v->isNoRe(),
                'p' => $v->isPercent(),
                's' => $v->isSharp(),
                'x' => $v->isX(),
            );
        }
        $this->title = Sanitize::html($this->_threads->TITLE);
        $link = "{$this->base}/article/{$this->_board->NAME}/{$gid}?p=%page%";
        if(false !== $auF)
            $link .= "&au=$au";
        $this->set("pageBar", $pagination->getPageBar($p, $link));
        $this->set("pagination", $pagination);

        $this->set("bName", $this->_board->NAME);
        $this->set("gid", $gid);
        $this->set("anony", $this->_board->isAnony());
        $this->set("tmpl", $this->_board->isTmplPost());
        $this->set("info", $info);
        $this->set("title", $this->title);
        $this->set('hasSyn', $hasSyn);
        $this->set("au", $au);
        $this->set("bm", $bm);
        //for the quick reply, raw encode the space
        $this->set("reid", $this->_threads->ID);
        if(!strncmp($this->_threads->TITLE, "Re: ", 4))
            $reTitle = $this->_threads->TITLE;
        else
            $reTitle = "Re: " . $this->_threads->TITLE;

        //hack for post with ajax,need utf-8 encoding
        $reTitle = nforum_iconv($this->encoding, 'utf-8', $reTitle);
        $this->set("reTitle", rawurlencode($reTitle));
        //for default search day
        $this->set("searchDay", Configure::read("search.day"));
        $this->set("searchDay", Configure::read("search.day"));
        $this->jsr[] = "window.user_post=" . ($this->_board->hasPostPerm($u) && !$this->_board->isDeny($u)?"true":"false") . ";";
    }

    public function post(){
        $article = $this->_postInit();

        $this->js[] = "forum.upload.js";
        $this->js[] = "forum.post.js";
        $this->css[] = "post.css";
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"发表文章");

        $reTitle = $reContent = "";
        if(false !== $article){
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
        }
        $u = User::getInstance();
        $sigOption = array();
        foreach(range(0, $u->signum) as $v){
            if($v == 0)
                $sigOption["$v"] = "不使用签名档";
            else
                $sigOption["$v"] = "使用第{$v}个";
        }
        App::import('Sanitize');
        $reTitle = Sanitize::html($reTitle);
        $reContent = Sanitize::html($reContent);
        $sigOption["-1"] = "使用随机签名档";
        $this->set("bName", $this->_board->NAME);
        $this->set("anony", $this->_board->isAnony());
        $this->set("outgo", $this->_board->isOutgo());
        $this->set("isAtt", $this->_board->isAttach());
        $this->set("titKey", $this->_board->getTitleKey());
        $this->set("subject", false === $article);
        $this->set("reTitle", $reTitle);
        $this->set("reContent", $reContent);
        $this->set("sigOption", $sigOption);
        $this->set("sigNow", $u->signature);

        $upload = Configure::read("article");
        $this->set("maxNum", $upload['att_num']);
        $this->set("maxSize", $upload['att_size']);
    }

    public function ajax_post(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $article = $this->_postInit();
        if(false === $article && $this->_board->isTmplPost())
            $this->error();

        if(!isset($this->params['form']['subject']))
            $this->error(ECode::$POST_NOSUB);
        if(!isset($this->params['form']['content']))
            $this->error(ECode::$POST_NOCON);
        $subject = rawurldecode(trim($this->params['form']['subject']));
        $subject = nforum_iconv('UTF-8', $this->encoding, $subject);
        if(strlen($subject) > 60)
            $subject = nforum_fix_gbk(substr($subject,0,60));
        $content = $this->params['form']['content'];
        $content = nforum_iconv('UTF-8', $this->encoding, $content);
        $sig = User::getInstance()->signature;
        $email = 0;$anony = null;$outgo = 0;
        if(isset($this->params['form']['signature']))
            $sig = intval($this->params['form']['signature']);
        if(isset($this->params['form']['email']))
            $email = 1;
        if(isset($this->params['form']['anony']) && $this->_board->isAnony())
            $anony = 1;
        if(isset($this->params['form']['outgo']) && $this->_board->isOutgo())
            $outgo = 1;
        try{
            if(false === $article)
                $id = Article::post($this->_board, $subject, $content, $sig, $email, $anony, $outgo);
            else
                $id = $article->reply($subject, $content, $sig, $email, $anony, $outgo);
            $gid = Article::getInstance($id, $this->_board);
            $gid = $gid->GROUPID;
        }catch(ArticlePostException $e){
            if(ECode::$POST_WAIT == $e->getMessage()){
                $ret['ajax_code'] = ECode::$POST_WAIT;
                $ret['list'][] = array("text" => '版面:' . $this->_board->DESC, "url" => "/board/" . $this->_board->NAME);
                $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
                $this->set('no_html_data', $ret);
                return;
            }else{
                $this->error($e->getMessage());
            }
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }

        $ret['ajax_code'] = ECode::$POST_OK;
        $ret['default'] = '/board/' .  $this->_board->NAME;
        $mode = $this->Cookie->read('BMODE');
        if($mode != null && $mode != BOARD::$THREAD) $ret['default'] .= '/mode/' . $this->Cookie->read('BMODE');
        $ret['list'][] = array("text" => '版面:' . $this->_board->DESC, "url" => $ret['default']);
        $ret['list'][] = array("text" => '主题:' . str_replace('Re: ', '', $subject), "url" => '/article/' .  $this->_board->NAME . '/' . $gid);
        $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
        $this->set('no_html_data', $ret);
    }

    public function ajax_delete(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
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
        $ret['ajax_code'] = ECode::$ARTICLE_DELOK;
        $ret['default'] = '/board/' .  $this->_board->NAME;
        $mode = $this->Cookie->read('BMODE');
        if($mode != null && $mode != BOARD::$THREAD) $ret['default'] .= '/mode/' . $this->Cookie->read('BMODE');
        $ret['list'][] = array("text" => '版面:' . $this->_board->DESC, "url" => $ret['default']);
        $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
        $this->set('no_html_data', $ret);
    }

    public function edit(){
        $this->_editInit();
        $id = $this->params['id'];

        $this->js[] = "forum.upload.js";
        $this->js[] = "forum.post.js";
        $this->css[] = "post.css";
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"编辑文章");

        $article = Article::getInstance($id, $this->_board);
        App::import('Sanitize');
        $title = Sanitize::html($article->TITLE);
        $content = Sanitize::html($article->getContent());
        $this->set("bName", $this->_board->NAME);
        $this->set("isAtt", $this->_board->isAttach());
        $this->set("titKey", $this->_board->getTitleKey());
        $this->set("subject", $article->isSubject());
        $this->set("title", $title);
        $this->set("content", $content);
        $this->set("eid", $id);

        $upload = Configure::read("article");
        $this->set("maxNum", $upload['att_num']);
        $this->set("maxSize", $upload['att_size']);
    }

    public function ajax_edit(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->_editInit();
        $id = $this->params['id'];
        if(!isset($this->params['form']['subject']))
            $this->error(ECode::$POST_NOSUB);
        if(!isset($this->params['form']['content']))
            $this->error(ECode::$POST_NOCON);
        $subject = trim($this->params['form']['subject']);
        $subject = nforum_iconv('UTF-8', $this->encoding, $subject);
        if(strlen($subject) > 60)
            $subject = nforum_fix_gbk(substr($subject,0,60));
        $content = trim($this->params['form']['content']);
        $content = nforum_iconv('UTF-8', $this->encoding, $content);
        $article = Article::getInstance($id, $this->_board);
        if(!$article->update($subject, $content))
            $this->error(ECode::$ARTICLE_EDITERROR);

        $ret['ajax_code'] = ECode::$ARTICLE_EDITOK;
        $ret['default'] = '/board/' .  $this->_board->NAME;
        $mode = $this->Cookie->read('BMODE');
        if($mode != null && $mode != BOARD::$THREAD) $ret['default'] .= '/mode/' . $this->Cookie->read('BMODE');
        $ret['list'][] = array("text" => '版面:' . $this->_board->DESC, "url" => $ret['default']);
        $ret['list'][] = array("text" => '主题:' . str_replace('Re: ', '', $subject), "url" => '/article/' .  $this->_board->NAME . '/' . $article->GROUPID);
        $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
        $this->set('no_html_data', $ret);
    }

    public function ajax_preview(){
        App::import('Sanitize');
        if(!isset($this->params['form']['subject']) || !isset($this->params['form']['content'])){
            $this->error();
        }

        $subject = rawurldecode(trim($this->params['form']['subject']));
        $subject = nforum_iconv('UTF-8', $this->encoding, $subject);
        if(strlen($subject) > 60)
            $subject = nforum_fix_gbk(substr($subject,0,60));
        $subject = Sanitize::html($subject);

        $content = $this->params['form']['content'];
        $content = nforum_iconv('UTF-8', $this->encoding, $content);
        $content = preg_replace("/\n/", "<br />", Sanitize::html($content));
        if(Configure::read("ubb.parse"))
            $content = XUBB::parse($content);
        $this->set('no_html_data', array("subject"=>$subject,"content"=>$content));
    }

    public function ajax_forward(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        if(!isset($this->params['form']['target']))
            $this->error(ECode::$USER_NONE);
        $id = intval($this->params['id']);
        $target = trim($this->params['form']['target']);
        $threads = isset($this->params['form']['threads']);
        $noref = isset($this->params['form']['noref']);
        $noatt = isset($this->params['form']['noatt']);
        $noansi = isset($this->params['form']['noansi']);
        $big5 = isset($this->params['form']['big5']);
        try{
            $article = Article::getInstance($id, $this->_board);
            if($threads){
                $t = Threads::getInstance($article->GROUPID, $this->_board);
                $t->forward($target, $t->ID, $noref, $noatt, $noansi, $big5);
            }else{
                $article->forward($target, $noatt, $noansi, $big5);
            }
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }catch(ArticleForwardException $e){
            $this->error($e->getMessage());
        }

        $ret['ajax_code'] = ECode::$ARTICLE_FORWARDOK;
        $this->set('no_html_data', $ret);
    }

    public function tmpl(){
        $article = $this->_postInit();
        App::import("vendor", "model/template");
        App::import('Sanitize');
        $this->js[] = "forum.tmpl.js";
        $this->css[] = "post.css";
        $this->_getNotice();
        $this->notice[] = array("url"=>"", "text"=>"模版发文");

        if(isset($this->params['url']['tmplid'])){
            //template question
            $id = trim($this->params['url']['tmplid']);
            try{
                $t = Template::getInstance($id, $this->_board);
            }catch(TemplateNullException $e){
                $this->error(ECode::$TMPL_ERROR);
            }
            $info = array();
            try{
                foreach(range(0, $t->CONT_NUM - 1) as $i){
                    $q = $t->getQ($i);
                    $info[$i] = array("text" => Sanitize::html($q['TEXT']), "len"=>$q['LENGTH']);
                }
            }catch(TemplateQNullException $e){
                $this->error();
            }
            $this->set("tmplId", $id);
            $this->set("bName", $this->_board->NAME);
            $this->set("info", $info);
            $this->set("num", $t->NUM);
            $this->set("tmplTitle", Sanitize::html($t->TITLE));
            $this->set("title", $t->TITLE_TMPL);
            $this->render("tmpl_que");
        }else{
            //template list
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

    public function ajax_tmpl(){
        $article = $this->_postInit();
        App::import("vendor", "model/template");
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        if(!isset($this->params['form']['tmplid']))
            $this->error(ECode::$TMPL_ERROR);
        $id = trim($this->params['form']['tmplid']);
        try{
            $t = Template::getInstance($id, $this->_board);
        }catch(TemplateNullException $e){
            $this->error(ECode::$TMPL_ERROR);
        }

        $val = $this->params['form']['q'];
        $val = nforum_iconv('UTF-8', $this->encoding, $val);
        $pre = $t->getPreview($val);
        $subject = $pre[0];
        $preview = $pre[1];
        $content = $pre[2];
        if($this->params['form']['pre'] == "0"){
            $u = User::getInstance();
            try{
                if(false === $article)
                    $id = Article::post($this->_board, $subject, $content, $u->signature);
                else
                    $id = $article->reply($subject, $content, $u->signature);
                $gid = Article::getInstance($id, $this->_board);
                $gid = $gid->GROUPID;
            }catch(ArticlePostException $e){
                $this->error($e->getMessage());
            }

            $ret['ajax_code'] = ECode::$POST_OK;
            $ret['default'] = "/board/" . $this->_board->NAME;
            $mode = $this->Cookie->read('BMODE');
            if($mode != null && $mode != BOARD::$THREAD) $ret['default'] .= '/mode/' . $this->Cookie->read('BMODE');
            $ret['list'][] = array("text" => '版面:' . $this->_board->DESC, "url" => $ret['default']);
            $ret['list'][] = array("text" => '主题:' . str_replace('Re: ', '', $subject), "url" => '/article/' .  $this->_board->NAME . '/' . $gid);
            $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
            $this->set('no_html_data', $ret);
        }else{
            App::import('Sanitize');
            $subject = Sanitize::html($subject);
            if(Configure::read("ubb.parse"))
                $content = XUBB::parse($content);
            $this->set('no_html_data', array("subject"=>$subject,"content"=>$preview, "reid"=>(false === $article)?0:$article->ID));
        }
    }

    public function ajax_single(){
        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        $id = $this->params['id'];

        try{
            $article = Article::getInstance($id, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }
        App::import('vendor', 'inc/wrapper');
        $wrapper = Wrapper::getInstance();
        $ret = $wrapper->article($article, array('single' => true, 'content' => false));
        $u = User::getInstance();
        $ret['allow_post'] = $this->_board->hasPostPerm($u);
        $ret['is_bm'] = $u->isBM($this->_board) || $u->isAdmin();
        $content = $article->getHtml(true);
        if(Configure::read("ubb.parse")){
            //remove ubb of nickname in first and title second line
            preg_match("'^(.*?<br \/>.*?<br \/>)'", $content, $res);
            $content = preg_replace("'(^.*?<br \/>.*?<br \/>)'", '', $content);
            $content = XUBB::remove($res[1]) . $content;
            $content = XUBB::parse($content);
        }
        $ret['content'] = $content;

        $this->set('no_html_data', $ret);
    }

    /**
     * op=g|m|;|top|%|x|sharp
     * top=m|um|x|ux|;|u;|d|dx
     */
    public function ajax_manage(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        if(!isset($this->params['form']['op'])
            && !isset($this->params['form']['top']))
            $this->error();

        $u = User::getInstance();
        if(!($u->isBM($this->_board) || $u->isAdmin())){
            $this->error(ECode::$ARTICLE_NOMANAGE);
        }

        $id = $this->params['id'];
        $ret['refresh'] = true;
        if(isset($this->params['form']['op'])){
            $op = explode('|', $this->params['form']['op']);
            try{
                $a = Article::getInstance($id, $this->_board);
                foreach($op as $v){
                    switch($v){
                        case 'g':
                            $a->manage(3);
                            break;
                        case 'm':
                            $a->manage(2);
                            break;
                        case ';':
                            $a->manage(4);
                            break;
                        case 'top':
                            if(!$a->isSubject())
                                $this->error(ECode::$ARTICLE_NOTORIGIN);
                            if($a->isReallyTop()){
                                $a->manage(1, true);
                            }else{
                                $a->manage(5);
                            }
                            break;
                        case '%':
                            $a->manage(7);
                            break;
                        case 'x':
                            $a->manage(8);
                            break;
                        case 'sharp':
                            $a->manage(9);
                            break;
                    }
                }
            }catch(ArticleNullException $e){
                $this->error(ECode::$ARTICLE_NONE);
            }catch(ArticleManageException $e){
                $this->error($e->getMessage);
            }
        }
        if(isset($this->params['form']['top'])){
            $top = explode('|', $this->params['form']['top']);
            $gid = isset($this->params['form']['gid'])?$this->params['form']['gid']:$id;
            try{
                $t = Threads::getInstance($gid, $this->_board);
                $s = ($gid == $id)?null:$id;
                if(in_array('d', $top)){
                    $t->manage(1, $s);
                    $ret['refresh'] = false;
                }else{
                    foreach($top as $v){
                        switch($v){
                            case 'm':
                                $t->manage(2, $s);
                                break;
                            case 'um':
                                $t->manage(3, $s);
                                break;
                            case 'x':
                                $t->manage(6, $s);
                                break;
                            case 'ux':
                                $t->manage(7, $s);
                                break;
                            case ';':
                                $t->manage(8, $s);
                                break;
                            case 'u;':
                                $t->manage(9, $s);
                                break;
                            case 'dx':
                                $t->manage(4, $s);
                                $ret['refresh'] = false;
                                break;
                        }
                    }
                }
            }catch(ThreadsNullException $e){
                $this->error(ECode::$ARTICLE_NONE);
            }catch(ArticleManageException $e){
                $this->error($e->getMessage);
            }
        }
        $ret['ajax_code'] = ECode::$SYS_AJAXOK;
        $ret['default'] = '/board/' .  $this->_board->NAME;
        $mode = $this->Cookie->read('BMODE');
        if($mode != null && $mode != BOARD::$THREAD) $ret['default'] .= '/mode/' . $this->Cookie->read('BMODE');
        $this->set('no_html_data', $ret);
    }

    public function ajax_deny(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        if(!isset($this->params['id']))
            $this->error(ECode::$ARTICLE_NONE);
        if(!isset($this->params['form']['reason']))
            $this->error(ECode::$DENY_NOREASON);
        if(!isset($this->params['form']['day']))
            $this->error(ECode::$DENY_INVALIDDAY);
        $id = $this->params['id'];
        $reason = nforum_iconv('utf-8', $this->encoding, $this->params['form']['reason']);
        $day = intval($this->params['form']['day']);
        if ($day < 1)
            $this->error(ECode::$DENY_INVALIDDAY);
        $u = User::getInstance();
        if (!($u->isBM($this->_board) || $u->isAdmin())) {
            $this->error(ECode::$ARTICLE_NOMANAGE);
        }
        try{
            $article = Article::getInstance($id, $this->_board);
            $article->addDeny($reason, $day);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NONE);
        }catch(ArticleManageException $e){
            $this->error($e->getMessage());
        }catch(BoardDenyException $e){
            $this->error($e->getMessage());
        }
    }

    //if there is reid,return reArticle,otherwise return false
    private function _postInit(){
        if($this->_board->isReadOnly()){
            $this->error(ECode::$BOARD_READONLY);
        }
        if(!$this->_board->hasPostPerm(User::getInstance())){
            $this->error(ECode::$BOARD_NOPOST);
        }
        if($this->_board->isDeny(User::getInstance())){
            $this->error(ECode::$POST_BAN);
        }
        if(isset($this->params['id']))
            $reID = intval($this->params['id']);
        else if(isset($this->params['form']['id']))
            $reID = intval($this->params['form']['id']);
        else if(isset($this->params['url']['id']))
            $reID = intval($this->params['url']['id']);
        else
            $reID = 0;
        if(empty($reID))
            return false;
        else
            $this->set('reid', $reID);
        if($this->_board->isNoReply())
            $this->error(ECode::$BOARD_NOREPLY);
        try{
            $reArticle = Article::getInstance($reID, $this->_board);
        }catch(ArticleNullException $e){
            $this->error(ECode::$ARTICLE_NOREID);
        }
        if($reArticle->isNoRe())
            $this->error(ECode::$ARTICLE_NOREPLY);
        return $reArticle;
    }

    //return the edit article
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
        $this->set('reid', $id);
        return $article;
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
        $url = "/board/{$this->_board->NAME}";
        $mode = $this->Cookie->read('BMODE');
        if($mode != null && $mode != BOARD::$THREAD) $url .= '/mode/' . $this->Cookie->read('BMODE');
        $this->notice[] = array("url"=>$url, "text"=>$this->_board->DESC);
    }
}
?>
