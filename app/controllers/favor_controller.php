<?php
/**
 * Favor controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/favor", "model/section"));
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
        $this->notice[] = array("url"=>"/fav", "text"=>"ÊÕ²Ø¼Ð");
    }

    public function ajax_show(){
        App::import('Sanitize');
        $level = $this->params['num'];
        try{
            $fav = Favor::getInstance($level);
        }catch(FavorNullException $e){
            $this->error(ECode::$USER_FAVERROR);
        }
        $this->cache(false);
        $ret = array();
        App::import('vendor', 'inc/wrapper');
        $wrapper = Wrapper::getInstance();
        if(!$fav->isNull()){
            $brds = $fav->getAll();
            $u = User::getInstance();
            foreach($brds as $k=>$v){
                $last = array();
                $last["id"] = $last["title"] = $last["owner"] = $last["date"] = false;
                if($v->hasReadPerm($u)){
                    $threads = $v->getTypeArticles(0, 1, Board::$ORIGIN);
                    if(!empty($threads)){
                        $threads = $threads[0];
                        $last = array(
                            "id" => $threads->ID,
                            "title" => Sanitize::html($threads->TITLE),
                            "owner" => $threads->isSubject()?$threads->OWNER:"Ô­ÌûÒÑÉ¾³ý",
                            "date" => date("Y-m-d H:i:s", $threads->POSTTIME)
                        );
                    }
                }
                if($v->NAME == ''){
                    $v = $wrapper->favorite($v);
                    $v['type'] = 'fav';
                    $v['name'] = $v['level'];
                }else if($v->isDir()){
                    $v = $wrapper->section(Section::getInstance($v));
                    $v['type'] = 'section';
                }else{
                    $v = $wrapper->board($v, array('status'=>true));
                    $v['type'] = 'board';
                }
                $v['last'] = $last;
                $ret[] = $v;
                continue;
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
                $ret[$k]['thnum'] = $v->getThreadsNum(); //threads num
                $ret[$k]['last'] = $last; //last post
                $ret[$k]['link'] = ($v->isDir()?"/fav/":"/board/") . $v->BID;
            }
        }
        $this->set('no_html_data',$ret);
        //no ajax status info
        $this->set('no_ajax_info', true);
    }

    public function ajax_change(){
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
                try{
                    $val = Board::getInstance($val);
                    if(!$fav->add($val, Favor::$BOARD))
                        $this->error();
                }catch(Exception $e){
                    $this->error(ECode::$BOARD_UNKNOW);
                }
                break;
            case "ad":
                if(!$fav->add(@iconv("utf-8", $this->encoding . "//TRANSLIT", $val), Favor::$DIR))
                    $this->error();
                break;
            case "db":
                try{
                    $val = Board::getInstance($val);
                    if(!$fav->delete($val, Favor::$BOARD))
                        $this->error();
                }catch(Exception $e){
                    $this->error(ECode::$BOARD_UNKNOW);
                }
                break;
            case "dd":
                if(!$fav->delete($val, Favor::$DIR))
                    $this->error();
                break;
        }
    }

    public function ajax_list(){
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
                    $fav = Favor::getInstance($root);
                }
            }catch (SectionNullException $e) {
                $this->_stop();
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
                            "t" => "<a href=\"javascript:void(0)\" title=\"{$v->DESC}\">{$v->DESC}</a>",
                            "id" => "f-" . $v->BID,
                        );
                    }elseif($v->isDir()){
                        $ret[] = array(
                            "t" => "<a href=\"{$this->base}/section/{$v->NAME}\" title=\"{$v->DESC}\">{$v->DESC}</a>",
                            "id" => "s-" . $v->NAME,
                        );
                    }else{
                        $ret[] = array(
                            "t" => "<a href=\"{$this->base}/board/{$v->NAME}\" title=\"{$v->DESC}\">{$v->DESC}</a>",
                        );
                    }
                }
            }
            $this->cache(true, $fav->wGetTime(), 10);
            $this->set('no_html_data', $ret);
            $this->set('no_ajax_info', true);
        }catch(FavorNullException $e){
            $this->_stop();
        }
    }
}
?>
