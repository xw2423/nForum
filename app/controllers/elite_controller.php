<?php
/**
 * Elite controller for nforum
 * actually i don't like this
 * @author xw
 */
App::import("vendor", array("model/board", "model/archive"));
class EliteController extends AppController {

    private $_root = "";
    public function beforeFilter(){
        parent::beforeFilter();
        $this->_root = Configure::read("elite.root");
    }

    public function path(){
        $this->js[] = "forum.board.js";
        $this->css[] = "board.css";

        $path = $boardName = $path_tmp = "";
        $articles = array();
        if(isset($this->params['url']['v']))
            $path = trim($this->params['url']['v']);

        $u = User::getInstance();
        $ret = bbs_read_ann_dir($path,$boardName,$path_tmp,$articles);
        switch ($ret) {
            case -1:
                if(!$this->ByrSession->isLogin)
                    $this->requestLogin();
                $this->error(ECode::$ELITE_NODIR);
            case -2:
                $this->error(ECode::$ELITE_DIRERROR);
            case -3:
                //on article
                break;
            case -9:
                //SYS_ERROR
                $this->error();
            default;
        }
        $path = $path_tmp;
        $parent = '';

        $up_dirs = array();
        $up_cnt = $this->_getUpdir($path,$boardName,$up_dirs);
        $start = 0;
        if ($up_cnt >= 2)
            $parent = $up_dirs[$up_cnt - 2];
        elseif ($up_cnt == 1) {
            $this->set("parent", "");
            $start = 1;
        }
        if ($boardName){
            try{
                $brd = Board::getInstance($boardName);
            }catch(BoardNullException $e){
                $this->error(ECode::$ELITE_NODIR);
            }
            if (!$brd->hasReadPerm($u)){
                if(!$this->ByrSession->isLogin)
                    $this->requestLogin();
                $this->error(ECode::$ELITE_NODIR);
            }
            $brd->setOnBoard();
            if ($brd->isNormal())
                $this->cache(true, @filemtime($path));
            $secs = Configure::read("section");
            $this->notice[] = array("url"=>"/section/{$brd->SECNUM}", "text"=>$secs[$brd->SECNUM][0]);
            $this->notice[] = array("url"=>"/board/{$brd->NAME}", "text"=>$brd->DESC);
        }
        if(count($articles) == 0)
            $info = false;
        else{
            App::import('Sanitize');
            foreach($articles as $v){
                $info[] = array(
                    "dir" => ($v['FLAG'] == 1)?"path":"file",
                    "title" => Sanitize::html($v['TITLE']),
                    "path" => urlencode($v['PATH']),
                    "bm" => $v['BM'],
                    "time" => date("Y-m-d", $v['TIME'])
                );
            }
        }
        if($parent != ""){
            $this->set("parent", urlencode($parent));
            $start = 1;
        }
        $this->set("start", $start);
        $this->set("info", $info);
        $this->notice[] = array("url" => "", "text" => "精华区列表");
    }

    public function file(){
        $this->css[] = "article.css";
        $this->css[] = "ansi.css";

        $path = Configure::read("elite.root") . "/";
        $boardName = $path_tmp = "";
        $articles = array();
        if(isset($this->params['url']['v'])){
            $path .= preg_replace("/^\//","", trim($this->params['url']['v']));
        }

        $u = User::getInstance();
        if(bbs_ann_traverse_check($path, $u->userid) < 0 ) {
            if(!$this->ByrSession->isLogin)
                $this->requestLogin();
            $this->error(ECode::$ELITE_NODIR);
        }
        $up_dirs = array();
        $up_cnt = $this->_getUpdir($path,$boardName,$up_dirs);
        if ($up_cnt >= 2){
            $parent = $up_dirs[$up_cnt - 2];
            $this->set("parent", urlencode($parent));
        } elseif ($up_cnt == 1) {
            $this->set("parent","");
        }
        if ($boardName){
            try{
                $brd = Board::getInstance($boardName);
            }catch(BoardNullException $e){
                $this->error(ECode::$ELITE_NODIR);
            }
            if (!$brd->hasReadPerm($u)){
                if(!$this->ByrSession->isLogin)
                    $this->requestLogin();
                $this->error(ECode::$ELITE_NODIR);
            }
            if($brd->isNormal())
                $this->cache(true, @filemtime($path));
        }
        $e = new Elite($path);
        if(isset($this->params['url']['pos'])){
            $pos = intval($this->params['url']['pos']);
            if($pos == 0)
                $this->_stop();
            $e->getAttach($pos); 
            $this->_stop();
        }
        $content = $e->getHtml(true);
        if(Configure::read("ubb.parse")){
            App::import("vendor", "inc/ubb");
            $content = preg_replace("'^(.*?<br \/>.*?<br \/>)'e", "XUBB::remove('\\1')", $content);
            $content = XUBB::parse($content);
        }
        $this->set("content", $content);
        $this->notice[] = array("url" => "", "text" => "精华区阅读");
    }

    //copy from wForum
    private function _getUpdir($path, &$board, &$up_dirs){
        $board = '';
        $path = ltrim(trim($path));
        if ($path[0]!='/') $path='/'.$path;
        if ($path[strlen($path)-1]=='/') $path =
            substr($path,0,strlen($path)-1);
        $up_dirs = array();
        $buf = '';
        $dirs = explode('/',$path);
        $j = 0;
        foreach($dirs as $dir) {
            if ($dir) {
                if (!strcmp('0Announce',$dir))
                    continue;
                $buf .= '/'.$dir;
                $up_dirs[] = $buf;
                if ($j == 2)
                    $board = $dir;
                $j ++;
            }
        }
        return count($up_dirs);
    }
}

/**
 * rabbish
 */
class Elite extends Archive{

    private $_path;

    public function __construct($path){
        parent::__construct(array());
        $this->_path = $path;
    }
    public function delete(){}
    public function update($title, $content){}
    public function getFileName(){
        return $this->_path;
    }
    public function getAttLink($pos){
        $base = Configure::read('site.prefix');
        return "$base/elite/file?v=" . urlencode(preg_replace('/' . Configure::read('elite.root') . '/', "", $this->_path)) . "&pos={$pos}";
    }
    public function addAttach($file, $fileName){}
    public function delAttach($num){}
}
?>
