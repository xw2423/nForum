<?php
/**
 * section controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/board", "model/section", "model/favor"));
class SectionController extends AppController {

    private $_sec;

    public function beforeFilter(){
        parent::beforeFilter();
    }

    public function index(){
        $this->js[] = "forum.board.js";
        $this->css[] = "board.css";

        App::import('Sanitize');
        if(!isset($this->params['num'])){
            $this->error(ECode::$SEC_NOSECTION);
        }
        try{
            $num = $this->params['num'];
            $this->_sec = Section::getInstance($num, Section::$NORMAL);
        }catch(SectionNullException $e){
            $this->error(ECode::$SEC_NOSECTION);
        }catch(BoardNullException $e){
            $this->error(ECode::$BOARD_NOBOARD);
        }
        $secs = $this->_sec->getAll();
        $ret = false;
        if(!$this->_sec->isNull()){
            foreach($secs as $brd){
                $threads = $brd->getTypeArticles(0, 1, Board::$ORIGIN);
                if(!empty($threads)){
                    $threads = $threads[0];
                    $last = array(
                        "id" => $threads->ID,
                        "title" => Sanitize::html($threads->TITLE),
                        "owner" => $threads->isSubject()?$threads->OWNER:"ԭ����ɾ��",
                        "date" => date("Y-m-d H:i:s", $threads->POSTTIME)
                    );
                }else{
                    $last["id"] = "";
                    $last["title"] = $last["owner"] = $last["date"] = "��";
                }
                $bms = split(" ", $brd->BM);
                foreach($bms as &$bm){
                    if(preg_match("/[^0-9a-zA-Z]/", $bm)){
                        $bm = array($bm, false);
                    }else{
                        $bm = array($bm, true);
                    }
                }
                $ret[] = array(
                    "name" => $brd->NAME,
                    "desc" => $brd->DESC,
                    "type" => $brd->isDir()?"section":"board",
                    "bms" => $bms,
                    "curNum" => $brd->CURRENTUSERS,
                    "todayNum" => $brd->getTodayNum(),
                    "threadsNum" => $brd->threadsNum,
                    "articleNum" => $brd->ARTCNT,
                    "last" => $last
                );
            }
        }
        $this->set("sec", $ret);
        $this->set("noBrd", ECode::msg(ECode::$SEC_NOBOARD));
        $this->set("secName", $this->_sec->getDesc());
        if(!$this->_sec->isRoot()){
            $parent = $this->_sec->getParent();
            $this->notice[] = array("url"=>"/section/{$parent->getName()}", "text"=>$parent->getDesc());
        }
        $this->notice[] = array("url"=>"/section/{$this->_sec->getName()}", "text"=>$this->_sec->getDesc());
        $this->notice[] = array("url"=>"javascript:void(0);", "text"=>$this->_sec->isRoot()?"�����б�":"Ŀ¼�б�");
    }

    public function slist(){
        $this->initAjax();
        $this->cache(true, strtotime(date("Y-m-d", time()+86400)));

        $ret = array();
        if(!isset($this->params['url']['root']))
            $this->_stop();
        $root = $this->params['url']['root'];
        try{
            if($root == "list-section"){
                $sections = Configure::read("section");    
                foreach($sections as $k=>$v){
                    $ret[] = array(
                        "t" => "<a href=\"{$this->base}/section/$k\">{$v[0]}</a>",
                        "id" => "sec-$k"
                        );
                }
                App::import("vendor", "inc/json");
                echo BYRJSON::encode($ret);
                $this->_stop();
            }else{
                $root = Section::getInstance(substr($root, 4), Section::$NORMAL);
            }
        }catch(SectionNullException $e){
            $this->_stop();
        }catch(BoardNullException $e){
            $this->_stop();
        }
        $sections = $root->getAll();
        foreach($sections as $v){
            $tmp = array();
            if($v->isDir()){
                $tmp['t'] = "<a href=\"{$this->base}/section/{$v->NAME}\">{$v->DESC}</a>";
                $tmp['id'] = 'sec-' . $v->NAME;
            }else{
                $tmp['t'] = "<a href=\"{$this->base}/board/{$v->NAME}\">{$v->DESC}</a>";
            }
            $ret[] = $tmp; 
        }
        App::import("vendor", "inc/json");
        echo BYRJSON::encode($ret);
        $this->_stop();
    }

    public function nlist(){
        $this->initAjax();

        $ret = array();
        if(!isset($this->params['url']['root']))
            $this->_stop();
        $root = $this->params['url']['root'];
        try{
            $sec = (substr($root,0,2) == "s-"?1:0);
            $root = ($root == "list-favor")?0:substr($root,2);
            try{
                if ($sec) {
                    $fav = Section::getInstance($root, Section::$NORMAL);
                } else {
                    $fav = Favor::getInstance($root, 2);
                }
            }catch(FavorNullException $e){
                $this->_stop();
            }
            $ret = array();
            if(!$fav->isNull()){
                $brds = $fav->getAll();
                foreach($brds as $v){
                    //user dir
                    if($v->NAME == ""){
                        $ret[] = array(
                            "t" => "<a href=\"javascript:void(0)\">{$v->DESC}</a>",
                            "id" => "f-" . $v->BID,
                        );
                    }elseif($v->isDir()){
                        $ret[] = array(
                            "t" => "<a href=\"{$this->base}/section/{$v->NAME}\">{$v->DESC}</a>",
                            "id" => "s-" . $v->NAME,
                        );
                    }else{
                        $ret[] = array(
                            "t" => "<a href=\"{$this->base}/board/{$v->NAME}\">{$v->DESC}</a>",
                        );
                    }
                }
            }
            $this->cache(true, $fav->wGetTime(), 10);
            App::import("vendor", "inc/json");
            echo BYRJSON::encode($ret);
            $this->_stop();
        }catch(FavorNullException $e){
            $this->_stop();
        }
    }

}
?>
