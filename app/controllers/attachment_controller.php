<?php
/**
 * Attachment controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/board", "model/article", "model/forum"));
class AttachmentController extends AppController {

    private $_board;
    public function __construct(){
        parent::__construct();
        $this->components[] = "Exif";
        $this->components[] = "Thumbnail";
    }

    public function beforeFilter(){
        if(isset($this->params['hash'])){
            $hash = str_replace(' ', '+', $this->params['hash']);
            $info = Forum::decodeAttHash($hash);
            if(false === $info)
                $this->error404(ECode::$SYS_NOFILE);
            $this->ByrSession->setSession($info['sid']);
        }
        //flash mode will post cookie data, so parse to system cookie first
        if ($this->RequestHandler->isFlash()) {
            if (isset($this->params['form']['cookie'])) {
                $cookie = $this->params['form']['cookie'];
                $prefix = Configure::read('cookie.prefix');
                $cookie = explode('; ', $cookie);
                foreach ($cookie as $c) {
                    list($name, $content) = split('=', $c);
                    if (preg_match("/^$prefix\[(.*)\]$/", $name, $matches)) {
                        $_COOKIE[$prefix][$matches[1]] = $content;
                    } else {
                        $_COOKIE[$name] = $content;
                    }
                }
            }
            if (isset($this->params['form']['emulate_ajax'])) {
                putenv('HTTP_X_REQUESTED_WITH=XMLHttpRequest');
            }
        }
        parent::beforeFilter();
    }

    public function download(){
        if(isset($this->params['hash'])){
            $hash = str_replace(' ', '+', $this->params['hash']);
            $info = Forum::decodeAttHash($hash);
            $name = $info['bid'];
            $id = $info['id'];
            $pos = $info['ap'];
            $mode = $info['ftype'];
            $num = $info['num'];
        }else if(isset($this->params['name']) && isset($this->params['id']) && isset($this->params['pos'])){
            if($this->ByrSession->Cookie->read("XWJOKE") == "" && Configure::read("article.att_check"))
                $this->error404(ECode::$SYS_NOFILE);
            $name = $this->params['name'];
            $mode = ('' == $this->params['mode'])?Board::$THREAD:intval($this->params['mode']);
            $num = $id = intval($this->params['id']);
            $pos = intval($this->params['pos']);
            $type = $this->params['type'];
        }else{
            $this->error404(ECode::$SYS_NOFILE);
        }


        $archive = null;
        App::import("vendor", "model/mail");
        try{
            if(in_array($name, get_class_vars("MailBox"))){
                $this->requestLogin();
                $box = new MailBox(User::getInstance(), $name);
                $archive = Mail::getInstance($id, $box);
            }else{
                $board = Board::getInstance($name);
                $board->setMode($mode);
                if(!$board->isSortMode()) $id = $num;
                if(!$board->hasReadPerm(User::getInstance()))
                    $this->error404(ECode::$SYS_NOFILE);
                $archive = Article::getInstance($id, $board);
                $file = $archive->getFileName();
                if($board->isNormal())
                    $this->cache(true, @filemtime($file));
            }
        }catch(Exception $e){
            $this->error404(ECode::$SYS_NOFILE);
        }

        //check thumbnail
        if(!empty($type))
            $this->Thumbnail->archive($type, $archive, $pos);

        $archive->getAttach($pos);
        $this->_stop();
    }

    public function edit(){
        //check sid
        if(false === $this->ByrSession->getSession()){
            $this->error404();
        }
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
        $root = Configure::read("section.{$this->_board->SECNUM}");
        $this->notice[] = array("url"=>"/section/{$this->_board->SECNUM}", "text"=>$root[0]);
        $boards = array(); $tmp = $this->_board;
        while(!is_null($tmp = $tmp->getDir())){
            $boards[] = array("url"=>"/section/{$tmp->NAME}", "text"=>$tmp->DESC);
        }
        foreach($boards as $v)
            $this->notice[] = $v;
        $this->notice[] = array("url"=>"/board/{$this->_board->NAME}", "text"=>$this->_board->DESC);
        $this->notice[] = array("url"=>"", "text"=>"±à¼­¸½¼þ");

        $this->js[] = "forum.upload.js";
        $this->css[] = "post.css";

        $article = Article::getInstance($id, $this->_board);
        App::import('Sanitize');
        $title = Sanitize::html($article->TITLE);
        $this->set("bName", $this->_board->NAME);
        $this->set("title", $title);
        $this->set("aid", $article->ID);
        $this->set("gid", $article->GROUPID);
        $this->set("sessionid", $this->ByrSession->getSession());

        $upload = Configure::read("article");
        $this->set("maxNum", $upload['att_num']);
        $this->set("maxSize", $upload['att_size']);
    }

    public function ajax_list(){
        $this->_attOpInit();
        $atts = $this->_attList();
        $this->set('no_html_data',$atts);
        $this->set('no_ajax_info',true);
    }

    public function ajax_add(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->_attOpInit();
        $u = User::getInstance();

        //get current file
        $isFile = false;
        if(isset($this->params['id'])){
            $id = $this->params['id'];
            try{
                $article = Article::getInstance($id, $this->_board);
                if(!$article->hasEditPerm($u))
                    $this->error(ECode::$ARTICLE_NOEDIT);
                $atts = $article->getAttList();
                $isFile = true;
            }catch(Exception $e){
                $this->error(ECode::$ARTICLE_NONE);
            }
        }else{
            $atts = Forum::listAttach();
        }
        $upload = Configure::read("article");
        $num = count($atts);$ret = array();$exif = '';
        if($num >= intval($upload['att_num']))
            $this->error(ECode::$ATT_NLIMIT);

        //init upload file
        if(isset($this->params['url']['name'])){
            //html5 mode
            $tmp_name = tempnam(CACHE, "upload_");
            file_put_contents($tmp_name, file_get_contents('php://input'));
            $file = array(
                'tmp_name' => $tmp_name,
                'name' => nforum_iconv('utf-8', $this->encoding, $this->params['url']['name']),
                'size' => filesize($tmp_name),
                'error' => 0
            );
        }else if(isset($this->params['form']['file'])
            && is_array($this->params['form']['file'])){
            //flash mode
            $file = $this->params['form']['file'];
            $file['name'] = nforum_iconv('utf-8', $this->encoding, $file['name']);
        }else{
            $this->error(ECode::$ATT_NONE);
        }

        //check upload file
        $errno = isset($file['error'])?$file['error']:UPLOAD_ERR_NO_FILE;
        switch($errno){
            case UPLOAD_ERR_OK:
                $tmpFile = $file['tmp_name'];
                $tmpName = $file['name'];
                if (!isset($tmp_name) && !is_uploaded_file($tmpFile))
                    $this->error(ECode::$ATT_NONE);

                $size = $file['size'];
                foreach($atts as $v){
                    if($v['name'] == $tmpName)
                        $this->error(ECode::$ATT_SAMENAME);
                    $size += intval($v['size']);
                    if($size > $upload['att_size'])
                        $this->error(ECode::$ATT_SLIMIT);
                }
                if(is_array(Configure::read("exif")) && in_array($this->_board->NAME, Configure::read("exif")) && @exif_imagetype($tmpFile) === 2)
                    $exif = $this->Exif->format($tmpFile);

                try{
                    if($isFile)
                        $article->addAttach($tmpFile, $tmpName);
                    else
                        Forum::addAttach($tmpFile, $tmpName);
                    if (isset($tmp_name))
                        @unlink($tmp_name);
                    $ret['no'] = $num + 1;
                    $ret['name'] = $tmpName;
                    $ret['size'] = $file['size'];
                    $ret['exif'] = $exif;
                    $this->set('no_html_data', $ret);
                    $this->set('ajax_code', ECode::$ATT_ADDOK);
                }catch(ArchiveAttException $e){
                    $this->error($e->getMessage());
                }catch(AttException $e){
                    $this->error($e->getMessage());
                }
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
            case UPLOAD_ERR_PARTIAL:
                $this->error(ECode::$ATT_SLIMIT);
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->error(ECode::$ATT_NONE);
                $msg = ECode::$ATT_NONE;
                break;
            default:
                $this->error(ECode::$SYS_ERROR);
        }
    }

    public function ajax_delete(){
        if(!$this->RequestHandler->isPost() && !$this->RequestHandler->isDelete())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->_attOpInit();
        $this->brief = true;
        $u = User::getInstance();
        if (isset($this->params['url']['name'])) {
            $attName = nforum_iconv('utf-8', $this->encoding, $this->params['url']['name']);
            try{
                if(isset($this->params['id'])){
                    $id = $this->params['id'];
                        $article = Article::getInstance($id, $this->_board);
                        if(!$article->hasEditPerm($u))
                            $this->error(ECode::$ARTICLE_NOEDIT);
                        $attNum = 0;
                        foreach($article->getAttList() as $k=>$v){
                            if($v['name'] == $attName){
                                $attNum = intval($k + 1);
                                break;
                            }
                        }
                        $article->delAttach($attNum);
                        $this->set("postUrl", "/{$article->ID}");
                }else{
                    Forum::delAttach($attName);
                }
                $this->set('ajax_code', ECode::$ATT_DELOK);
            }catch(ArchiveAttException $e){
                $this->error($e->getMessage());
            }catch(AttException $e){
                $this->error($e->getMessage());
            }catch(Exception $e){
                $this->error(ECode::$ATT_NAMEERROR);
            }
        }else{
            $this->error(ECode::$ATT_NAMEERROR);
        }
    }

    private function _attOpInit(){
        $this->cache(false);
        if(!isset($this->params['name']))
            $this->error(ECode::$BOARD_UNKNOW);

        $name = $this->params['name'];
        $u = User::getInstance();
        try{
            $brd = Board::getInstance($name);
            if(!$brd->hasPostPerm($u) || !$brd->isAttach())
                $this->error(ECode::$BOARD_NOPERM);
        }catch(Exception $e){
            $this->error(ECode::$BOARD_UNKNOW);
        }
        $this->_board = $brd;
        $this->set("bName", $this->_board->NAME);
    }

    //return array(array(["name"],["size"],["pos"]))
    private function _attList(){
        $u = User::getInstance();
        if(isset($this->params['id'])){
            $id = $this->params['id'];
            try{
                $article = Article::getInstance($id, $this->_board);
                if(!$article->hasEditPerm($u))
                    $this->error(ECode::$ARTICLE_NOEDIT);
                $atts = $article->getAttList();
                $this->set("postUrl", "/{$article->ID}");
            }catch(Exception $e){
                $this->error(ECode::$ARTICLE_NONE);
            }
        }else{
            $atts = Forum::listAttach();
        }
        return $atts;
    }
}
?>
