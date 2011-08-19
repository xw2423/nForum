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
    }

    public function download(){
        if(!isset($this->params['name']) 
        || !isset($this->params['id'])
        || !isset($this->params['pos']))
            $this->error(ECode::$SYS_NOFILE);

        if($this->ByrSession->Cookie->read("XWJOKE") == "" && Configure::read("article.att_check"))
            $this->error(ECode::$SYS_NOFILE);
        $name = $this->params['name'];
        $id = $this->params['id'];
        $pos = $this->params['pos'];

        $archive = null;
        App::import("vendor", "model/mail");
        try{
            if(in_array($name, get_class_vars("MailBox"))){
                $this->requestLogin();
                    $box = new MailBox(User::getInstance(), $name);
                    $archive = Mail::getInstance($id, $box);    
            }else{
                $board = Board::getInstance($name);
                if(!$board->hasReadPerm(User::getInstance()))
                    $this->error(ECode::$SYS_NOFILE);
                $archive = Article::getInstance($id, $board);
                $file = $archive->getFileName();
                if($board->isNormal())
                    $this->cache(true, @filemtime($file));
            }
        }catch(Exception $e){
            $this->error(ECode::$SYS_NOFILE);
        }
        $archive->getAttach($pos);
        $this->_stop();
    }

    public function index(){
        $this->_attOpInit();
        $this->brief = true;
        $this->_attList();
    }

    public function add(){
        $this->_attOpInit();
        $this->brief = true;
        $isFile = false;
        $u = User::getInstance();
        if(isset($this->params['id'])){
            $id = $this->params['id'];
            try{
                $article = Article::getInstance($id, $this->_board);
                if(!$article->hasEditPerm($u))
                    $this->error(ECode::$XW_JOKE);
                $atts = $article->getAttList();
                $this->set("postUrl", "/{$article->ID}");
                $isFile = true;
            }catch(Exception $e){
                $this->error(ECode::$XW_JOKE);
            }
        }else{
            $atts = Forum::listAttach();
        }
        $num = count($atts);
        $size = 0;
        foreach($atts as $v){
            $size += intval($v['size']);
        }
        $upload = Configure::read("article");
        if($num >= intval($upload['att_num'])){ 
            $this->set("msg", ECode::msg(ECode::$ATT_NLIMIT));
            return;
        }

        if (isset($this->params['form']['attachfile'])) {
            $errno=$this->params['form']['attachfile']['error'];
        } else {
            $errno = UPLOAD_ERR_PARTIAL;
        }
        switch($errno){
            case UPLOAD_ERR_OK:
                $tmpFile = $this->params['form']['attachfile']['tmp_name'];
                $tmpName = $this->params['form']['attachfile']['name'];
                if (!is_uploaded_file($tmpFile)) {
                    $msg = ECode::$ATT_NLIMIT;
                    break;
                }
                if(($size + filesize($tmpFile)) > intval($upload['att_size'])){
                    $msg = ECode::$ATT_SLIMIT;
                    break;
                }
                $exif = false;
                if(is_array(Configure::read("exif")) && in_array($this->_board->NAME, Configure::read("exif")) && exif_imagetype($tmpFile) === 2){
                    $exif = $this->Exif->format($tmpFile);
                }
                try{
                    if($isFile)
                        $article->addAttach($tmpFile, $tmpName);
                    else
                        Forum::addAttach($tmpFile, $tmpName);
                    $this->set("new", $num + 1);
                    if($exif !== false)
                        $this->set("exif", $exif);
                    $msg = ECode::$ATT_ADDOK;
                }catch(ArchiveAttException $e){
                    $msg = $e->getMessage();
                }catch(AttException $e){
                    $msg = $e->getMessage();
                }
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
            case UPLOAD_ERR_PARTIAL:
                $msg = ECode::$ATT_SLIMIT;
                break;
            case UPLOAD_ERR_NO_FILE:
                $msg = ECode::$ATT_NONE;
                break;
            default:
                $msg = ECode::$SYS_ERROR;
        }
        $this->set("msg", ECode::msg($msg));
        $this->_attList();
        $this->render("index");
    }
    
    public function delete(){
        $this->_attOpInit();
        $this->brief = true;
        $u = User::getInstance();
        if (isset($this->params['url']['name'])) {
            $attName = strval($this->params['url']['name']);
            try{
                if(isset($this->params['id'])){
                    $id = $this->params['id'];
                        $article = Article::getInstance($id, $this->_board);
                        if(!$article->hasEditPerm($u))
                            $this->error(ECode::$XW_JOKE);
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
                $msg = ECode::$ATT_DELOK;
            }catch(ArchiveAttException $e){
                $msg = $e->getMessage();
            }catch(AttException $e){
                $msg = $e->getMessage();
            }catch(Exception $e){
                $this->error(ECode::$XW_JOKE);
            }
        }else{
            $msg = ECode::$ATT_NAMEERROR;
        }
        $this->set("msg", ECode::msg($msg));
        $this->_attList();
        $this->render("index");
    }

    private function _attOpInit(){
        $this->cache(false);
        $this->js[] = "forum.autofix.js";
        if(!isset($this->params['name'])){
            $this->error(ECode::$XW_JOKE);
        }
        $name = $this->params['name'];
        $u = User::getInstance();
        try{
            $brd = Board::getInstance($name);
            if(!$brd->hasPostPerm($u) || !$brd->isAttach())
                $this->error(ECode::$XW_JOKE);
        }catch(Exception $e){
            $this->error(ECode::$XW_JOKE);
        }
        $this->_board = $brd;
        $this->set("bName", $this->_board->NAME);
    }

    private function _attList(){
        $u = User::getInstance();
        if(isset($this->params['id'])){
            $id = $this->params['id'];
            try{
                $article = Article::getInstance($id, $this->_board);
                if(!$article->hasEditPerm($u))
                    $this->error(ECode::$XW_JOKE);
                $atts = $article->getAttList();
                $this->set("postUrl", "/{$article->ID}");
            }catch(Exception $e){
                $this->error(ECode::$XW_JOKE);
            }
        }else{
            $atts = Forum::listAttach();
        }
        $num = count($atts);
        $size = 0;
        foreach($atts as &$v){
            $size += intval($v['size']);
            $v['size'] = nforum_size_format($v['size']);
        }
        $upload = Configure::read("article");
        if($num >= intval($upload['att_num']) || $size >= intval($upload['att_size']))
            $this->set("disable", true);
        $this->set("atts", $atts);
        $this->set("size", nforum_size_format($size));
        $this->set("num", $num);
        $this->set("maxNum", $upload['att_num']);
        $this->set("maxSize", nforum_size_format($upload['att_size']));
    }
}
?>
