<?php
App::import("vendor", array("model/board", "model/article"));
class AttachmentController extends ApiAppController {

    private $_board;

    public function __construct(){
        parent::__construct();
        $this->components[] = "Thumbnail";
    }

    public function download(){
        if(!isset($this->params['name'])
        || !isset($this->params['id'])
        || !isset($this->params['pos']))
            $this->error(ECode::$SYS_NOFILE);

        $name = $this->params['name'];
        $mode = ('' == $this->params['mode'])?Board::$THREAD:intval($this->params['mode']);
        $id = $this->params['id'];
        $pos = $this->params['pos'];
        $type = $this->params['type'];

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

        //check thumbnail
        if(!empty($type))
            $this->Thumbnail->archive($type, $archive, $pos);

        $archive->getAttach($pos);
        $this->_stop();
    }

    public function index(){
        $this->_attOpInit();
        $u = User::getInstance();
        if(isset($this->params['id'])){
            $id = $this->params['id'];
            try{
                $article = Article::getInstance($id, $this->_board);
                if(!$article->hasEditPerm($u))
                    $this->error(ECode::$ARTICLE_NOEDIT);
            }catch(ArticleNullException $e){
                $this->error(ECode::$ARTICLE_NONE);
            }
        }else{
            $article = Forum::listAttach();
        }

        $wrapper = Wrapper::getInstance();
        $this->set('data', $wrapper->attachment($article));
    }

    public function add(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->_attOpInit();
        $isFile = false;
        $u = User::getInstance();
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
        $num = count($atts);
        $size = 0;
        foreach($atts as $v){
            $size += intval($v['size']);
        }
        $upload = Configure::read("article");
        if($num >= intval($upload['att_num'])){
            $this->error(ECode::$ATT_NLIMIT);
        }

        if (isset($this->params['form']['file'])) {
            $errno=$this->params['form']['file']['error'];
        } else {
            $errno = UPLOAD_ERR_PARTIAL;
        }
        switch($errno){
            case UPLOAD_ERR_OK:
                $tmpFile = $this->params['form']['file']['tmp_name'];
                $tmpName = $this->params['form']['file']['name'];
                if (!is_uploaded_file($tmpFile)) {
                    $msg = ECode::$ATT_NONE;
                    break;
                }
                if(($size + filesize($tmpFile)) > intval($upload['att_size'])){
                    $msg = ECode::$ATT_SLIMIT;
                    break;
                }
                try{
                    if($isFile){
                        $article->addAttach($tmpFile, $tmpName);
                        $article = Article::getInstance($id, $this->_board);
                    }else{
                        Forum::addAttach($tmpFile, $tmpName);
                        $article = Forum::listAttach();
                    }
                    $wrapper = Wrapper::getInstance();
                    $this->set('data', $wrapper->attachment($article));
                    return;
                }catch(ArticleNullException $e){
                    $this->error(ECode::$ARTICLE_NONE);
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
        $this->error($msg);
    }

    public function delete(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->_attOpInit();
        $u = User::getInstance();
        if(!isset($this->params['form']['name']))
            $this->error(ECode::$ATT_NAMEERROR);
        $attName = strval($this->params['form']['name']);
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
                    $article = Article::getInstance($id, $this->_board);
            }else{
                Forum::delAttach($attName);
                $article = Forum::listAttach();
            }
            $wrapper = Wrapper::getInstance();
            $this->set('data', $wrapper->attachment($article));
            return;
        }catch(ArchiveAttException $e){
            $msg = $e->getMessage();
        }catch(AttException $e){
            $msg = $e->getMessage();
        }catch(Exception $e){
            $this->error();
        }
    }

    private function _attOpInit(){
        $this->requestLogin();
        $name = $this->params['name'];
        $u = User::getInstance();
        try{
            $brd = Board::getInstance($name);
            if(!$brd->hasPostPerm($u) || !$brd->isAttach())
                $this->error(ECode::$ARTICLE_NOEDIT);
        }catch(BoardNullException $e){
            $this->error(ECode::$BOARD_NONE);
        }
        $this->_board = $brd;
        App::import("vendor", "model/forum");
    }
}
?>
