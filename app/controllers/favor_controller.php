<?php
/**
 * Favor controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/favor"));
class FavorController extends AppController {

    public function beforeFilter(){
        parent::beforeFilter();
        $this->requestLogin();
        if(!isset($this->params['num']))
            $this->params['num'] = 0;
    }

    public function index(){
        $this->js[] = "forum.fav.js";
        $this->css[] = "favor.css";
        $this->notice[] = array("url"=>"/fav", "text"=>"收藏夹");
    }

    public function show(){
        $this->initAjax();
        App::import('Sanitize');
        $level = $this->params['num'];
        try{
            $fav = Favor::getInstance($level);
        }catch(FavorNullException $e){
            $this->error(ECode::$USER_FAVERROR);
        }
        $this->cache(false);
        $ret = array();
        if(!$fav->isNull()){
            $brds = $fav->getAll();
            foreach($brds as $k=>$v){
                $threads = $v->getLastThreads();
                if(!is_null($threads)){
                    $last = array(
                        "id" => $threads->ID,
                        "title" => Sanitize::html($threads->TITLE),
                        "owner" => $threads->isSubject()?$threads->OWNER:"原帖已删除",
                        "date" => date("Y-m-d H:i:s", $threads->LAST->POSTTIME)
                    );
                }else{
                    $last["id"] = $last["title"] = $last["owner"] = $last["date"] = "无";
                }
                $ret[$k]['name'] = $v->NAME;
                $ret[$k]['desc'] = $v->DESC;
                $ret[$k]['dir'] = $v->isDir()?1:0;
                $ret[$k]['class'] = $v->CLASS;
                $ret[$k]['bm'] = $v->BM;
                $ret[$k]['pos'] = $v->NPOS;
                $ret[$k]['bid'] = $v->BID;
                $ret[$k]['num'] = $v->ARTCNT; //article num
                $ret[$k]['pnum'] = $v->CURRENTUSERS;//online
                $ret[$k]['tnum'] = $v->getTodayNum(); //totay article num
                $ret[$k]['thnum'] = $v->threadsNum; //threads num
                $ret[$k]['last'] = $last; //last post
                $ret[$k]['link'] = ($v->isDir()?"/fav/":"/board/") . $v->BID;
            }
        }
        $this->success(null,$ret);
    }

    public function change(){
        $this->initAjax();
        if(!isset($this->params['form']['ac']) || !isset($this->params['form']['v']))
            $this->error();
        $action = $this->params['form']['ac'];
        $val = $this->params['form']['v'];
        $level = $this->params['num'];
        try{
            $fav = Favor::getInstance($level);
        }catch(FavorNullException $e){
            $this->error(ECode::$USER_FAVERROR);
        }
        if($val == "")
            $this->error();
        switch($action){
            case "ab":
                if(!$fav->add($val, Favor::$BOARD))
                    $this->error();
                break;
            case "ad":
                if(!$fav->add(iconv("utf-8", "gbk//TRANSLIT", $val), Favor::$DIR))
                    $this->error();
                break;
            case "db":
                if(!$fav->delete($val, Favor::$BOARD))
                    $this->error();
                break;
            case "dd":
                if(!$fav->delete($val, Favor::$DIR))
                    $this->error();
                break;
        }
        $this->success(null, array("v" => $val));
    }

    public function flist(){
        $this->initAjax();

        $ret = array();
        if(!isset($this->params['url']['root']))
            $this->_stop();
        $root = $this->params['url']['root'];
        try{
            $root = ($root == "list-favor")?0:substr($root,2);
            try{
                $fav = Favor::getInstance($root);
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
                            "text" => $v->DESC,
                            "id" => "f-" . $v->NPOS,
                            "classes" => "xtree-dir",
                            "hasChildren"=> true
                        );
                    }else{
                        $ret[] = array(
                            "text" => "<samp class=\"ico-pos-dot\"></samp><a href=\"{$this->base}/".($v->isDir()?"section":"board")."/{$v->NAME}\">{$v->DESC}</a>",
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
