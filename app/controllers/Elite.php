<?php
/**
 * Elite controller for nforum
 * actually i don't like this
 * @author xw
 */
load(array("model/board", "model/archive"));
class EliteController extends NF_Controller {

    public function pathAction(){
        $this->js[] = "forum.board.js";
        $this->js[] = "forum.elite.js";
        $this->css[] = "board.css";

        Forum::setUserMode(BBS_MODE_CSIE_ANNOUNCE);
        $path = $boardName = $path_tmp = "";
        $articles = array();
        if(isset($this->params['url']['v']))
            $path = trim($this->params['url']['v']);

        $u = User::getInstance();
        $ret = bbs_read_ann_dir($path,$boardName,$path_tmp,$articles);
        switch ($ret) {
            case -1:
                if(!NF_Session::getInstance()->isLogin)
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
                if(!NF_Session::getInstance()->isLogin)
                    $this->requestLogin();
                $this->error(ECode::$ELITE_NODIR);
            }
            $brd->setOnBoard();
            if ($brd->isNormal())
                $this->cache(true, @filemtime($path));
            $secs = c("section");
            $this->notice[] = array("url"=>"/section/{$brd->SECNUM}", "text"=>$secs[$brd->SECNUM][0]);
            $this->notice[] = array("url"=>"/board/{$brd->NAME}", "text"=>$brd->DESC);
        }
        if(count($articles) == 0)
            $info = false;
        else{
            foreach($articles as $v){
                $info[] = array(
                    "dir" => ($v['FLAG'] == 1)?"path":"file",
                    "title" => nforum_html($v['TITLE']),
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


    public function fileAction(){
        if(!isset($this->params['url']['pos'])
            && !preg_match("/ajax_file$/", $this->getRequest()->url)
            && !$this->getRequest()->spider){
            $this->redirect('elite/path?v=' . preg_replace("|/([^/]+)/*$|","&f=", trim($this->params['url']['v'])) . trim($this->params['url']['v']));
        }

        $path = c("elite.root") . "/";
        $boardName = "";
        $articles = array();
        if(isset($this->params['url']['v'])){
            $path .= preg_replace("/^\//","", trim($this->params['url']['v']));
        }

        $u = User::getInstance();
        if(bbs_ann_traverse_check($path, $u->userid) < 0 ) {
            if(!NF_Session::getInstance()->isLogin)
                $this->requestLogin();
            $this->error(ECode::$ELITE_NODIR);
        }
        $up_dirs = array();
        $up_cnt = $this->_getUpdir($path,$boardName,$up_dirs);
        if ($boardName){
            try{
                $brd = Board::getInstance($boardName);
            }catch(BoardNullException $e){
                $this->error(ECode::$ELITE_NODIR);
            }
            if (!$brd->hasReadPerm($u)){
                if(!NF_Session::getInstance()->isLogin)
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
        $subject = '';
        if(preg_match("|标&nbsp;&nbsp;题: ([\s\S]*?)<br|", $content, $subject))
            $subject = trim($subject[1]);
        if(c("ubb.parse")){
            load("inc/ubb");
            $content = preg_replace("'^(.*?<br \/>.*?<br \/>)'e", "XUBB::remove('\\1')", $content);
            $content = XUBB::parse($content);
        }
        $this->set(array(
            'subject' => $subject
            ,'content' => $content
        ));
    }

    public function ajax_fileAction(){
        $this->fileAction();
        $this->set("no_html_data", array('subject'=>$this->get('subject'), 'content'=>$this->get('content')));
    }

    public function downloadAction(){
        if(!isset($this->params['url']['pos']))
            $this->_stop();
        $this->fileAction();
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
                if ($j == 2 && !strcmp($up_dirs[0], 'groups'))
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
        return "/download?v=" . urlencode(preg_replace('/' . c('elite.root') . '/', "", $this->_path)) . "&pos={$pos}";
    }
    public function getAttHtml($thumbnail = ''){
        $base = c('site.prefix');
        $ret = parent::getAttHtml($thumbnail);
        foreach($ret as &$v){
            $v = str_replace($base . '/att', $base . '/elite', $v);
        }
        return $ret;
    }
    public function addAttach($file, $fileName){}
    public function delAttach($num){}
}
