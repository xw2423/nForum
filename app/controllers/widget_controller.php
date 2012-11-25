<?php
/**
 * widget controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/favor", "model/section", "model/widget"));
class WidgetController extends AppController {

    public function ajax_widget(){
        if(!isset($this->params['name']))
            $this->error();
        $name = $this->params['name'];
        if(!$this->ByrSession->isLogin && !in_array($name, array_keys(Configure::read("widget.default"))))
            $this->error();
        try{
            $widget = Widget::getInstance($name);
            if(!$widget->wHasPerm(User::getInstance()))
                $widget = new EWidget('你无权访问此应用');
        }catch(WidgetNullException $e){
            $widget = new EWidget();
        }
        $this->cache(true, $widget->wGetTime(), 10);
        $arr = array(
            "id" => $name,
            "list" => $widget->wGetList()
        );
        $this->set('no_html_data', $arr);
    }

    /**
     * set widget property
     * url param:
     * w: widget id
     * t: action type
     * c: col num
     * r: row num
     * ti: title
     * co: color num
     */
    public function ajax_set(){
        if(!$this->ByrSession->isLogin)
            $this->_stop();
        if(!isset($this->params['url']['w']) ||
            !isset($this->params['url']['t']))
            $this->_stop();
        $t = $this->params['url']['t'];
        $wid = $this->params['url']['w'];

        $u = User::getInstance();
        switch($t){
            case Widget::$ADD:
                if(!isset($this->params['url']['c']) ||
                    !isset($this->params['url']['r']))
                    $this->error();
                $col = intval($this->params['url']['c']);
                $row = intval($this->params['url']['r']);
                try{
                    //add widget need check widget
                    //but move,delete,modify only handle database
                    $widget = Widget::getInstance($wid);
                    $title = $widget->wGetTitle();
                    $title = $title["text"];
                    $color = 0;
                    if(isset($this->params['url']['ti']))
                        $title = $this->params['url']['ti'];
                    if(isset($this->params['url']['co']))
                        $color = $this->params['url']['co'];
                    $title = urldecode($title);
                    $title = nforum_iconv('utf-8', $this->encoding, $title);
                    $color = ($color < count(Configure::read("widget.color")) && $color >= 0)?$color:0;
                    Widget::wAdd($u, $widget->wGetName(), $title, $color, $col, $row);
                }catch(Exception $e){
                    $this->error();
                }
                break;

            case Widget::$DELETE:
                try{
                    Widget::wDelete($u, $wid);
                }catch(WidgetOpException $e){
                    $this->error();
                }
                break;

            case Widget::$MOVE:
                if(!isset($this->params['url']['c']) ||
                    !isset($this->params['url']['r']))
                    $this->_stop();
                $col = intval($this->params['url']['c']);
                $row = intval($this->params['url']['r']);
                try{
                    Widget::wMove($u, $wid, $col, $row);
                }catch(Exception $e){
                    $this->_stop();
                }
                break;

            case Widget::$MODIFY:
                if(!isset($this->params['url']['ti']) ||
                !isset($this->params['url']['co']))
                    $this->_stop();
                $color = $this->params['url']['co'];
                if ($color >= count(Configure::read("widget.color")) ||
                    $color < 0)
                    $this->_stop();
                $title = urldecode(urldecode($this->params['url']['ti']));
                $title = nforum_iconv('utf-8', $this->encoding, $title);
                try{
                    Widget::wSet($u, $wid, $title, $color);
                    $this->set('no_html_data', array('n'=>$wid,'t'=>$title, 'c'=>$color));
                }catch(Exception $e){
                    $this->_stop();
                }
                break;
        }
    }

    public function add(){
        $this->requestLogin();
        $this->js[] = "forum.widget.js";
        $this->css[] = "widget.css";
        $this->notice[] = array("url"=>"/widget/add", "text"=>"个性化首页");

        $type = "section";
        if(isset($this->params['url']['t'])){
            $type = $this->params['url']['t'];
        }

        try{
            $widgets = Widget::wGet(User::getInstance());
        }catch(UserNullException $e){
            $this->error(ECode::$SYS_NOLOGIN);
        }catch(Exception $e){
            $this->error();
        }

        switch($type){
            case 'board':
                $secs = Configure::read('section');
                foreach($secs as $k => $v){
                    $filter["$type-$k"] = $v[0];
                }
                $this->set("filter", $filter);
                $this->set("selected", 0);
                break;
            case 'favor':
                //todo:more level
                $filter["favor-0"] = "根目录";
                /*
                $favor = Favor::getInstance();
                foreach($favor->getDir() as $v){
                    if($v->NAME == "")
                        $filter["$type-" . $v->BID] = $v->DESC;
                }
                */
                $this->set("filter", $filter);
                $this->set("selected", "favor-0");
                break;
            case 'ext':
                $ext = Configure::read('widget.ext');
                foreach($ext as $k => $v){
                    if(!isset($first))
                        $first = $k;
                    $filter["$type-$k"] = $v[0];
                }
                $this->set("filter", $filter);
                $this->set("selected", "$type-$first");
                $this->set("search", true);
                break;
            default:
                $type = "section";
                $filter = array("$type-0"=>"分区", "$type-1"=>"目录");
                $this->set("filter", $filter);
                $this->set("selected", 0);
        }

        $ret = array();
        $colors = Configure::read('widget.color');
        foreach($colors as $k=>$v){
            $ret[$k] = $v[1];
        }
        $u = User::getInstance();
        $this->set("line3", $u->getCustom("userdefine1", 31));
        $this->set("colors", $ret);
        $this->set("color", 0);
        $this->set("type", $type);
    }

    public function ajax_list(){
        $this->requestLogin();

        if(!isset($this->params['url']['t'])){
            $this->error();
        }
        $type = $this->params['url']['t'];

        if(!isset($this->params['url']['tt'])){
            $this->error();
        }
        $ret = array();

        try{
            $widgets = Widget::wGet(User::getInstance());
            $my = array();
            foreach($widgets as $v){
                $my[] = $v["name"];
            }
            switch($type){
                case 'section':
                    //$tt is for 0:root or 1:dir
                    $tt = $this->params['url']['tt'];
                    if($tt != 0 && $tt != 1){
                        $this->error();
                    }

                    $secs = Configure::read('section');
                    foreach($secs as $k=>$v){
                        $w = Widget::getInstance("section-" . $k);
                        if($tt == 0){
                            if(!in_array($w->wGetName(), $my)){
                                $title = $w->wGetTitle();
                                $title = $title["text"];
                                $ret[] = array('wid' => $w->wGetName(), 'title' => $title, 'p'=> file_exists(IMAGES . 'app/icon/'.$w->wGetName().'.png')?$w->wGetName():"default");
                            }
                        }else if($tt == 1){
                            foreach($w->getDir() as $dir){
                                $ww = Widget::getInstance("section-" .  $dir->NAME);
                                if(!in_array($ww->wGetName(), $my)){
                                    $title = $ww->wGetTitle();
                                    $title = $title["text"];
                                    $ret[] = array('wid' => $ww->wGetName(), 'title' => $title, 'p'=> file_exists(IMAGES . 'app/icon/'.$ww->wGetName().'.png')?$ww->wGetName():"default");
                                }
                            }
                        }
                    }
                    break;
                case 'favor':
                    //tt is for favor level
                    //favor is only one level!!! the structure error!!!
                    $tt = intval($this->params['url']['tt']);
                    $favor = Favor::getInstance($tt);
                    if(!in_array($favor->wGetName(), $my)){
                        $title = $favor->wGetTitle();
                        $title = $title["text"];
                        $ret[] = array('wid' => $favor->wGetName(), 'title' => $title, 'p'=> file_exists(IMAGES . 'app/icon/'.$favor->wGetName().'.png')?$favor->wGetName():"default");
                    }
                    foreach($favor->getDir() as $w){
                        if(!in_array("favor-" . $w->BID, $my) && $w->NAME == ""){
                            $ret[] = array('wid' => "favor-" . $w->BID, 'title' => $w->DESC, 'p'=> "default");
                        }
                    }
                    break;
                case 'board':
                    if(!isset($this->params['url']['tt'])){
                        $this->error();
                    }
                    //$tt is for section num
                    $tt = intval($this->params['url']['tt']);
                    $secs = Configure::read('section');
                    if(!in_array($tt, array_keys($secs))){
                        $this->error();
                    }
                    $w = Section::getInstance($tt, Section::$ALL);
                    foreach($w->getList() as $brd){
                        $ww = Widget::getInstance("board-" .  $brd->NAME);
                        if(!in_array($ww->wGetName(), $my)){
                            $title = $ww->wGetTitle();
                            $title = $title["text"];
                            $ret[] = array('wid' => $ww->wGetName(), 'title' => $title, 'p'=> file_exists(IMAGES . 'app/icon/'.$ww->wGetName().'.png')?$ww->wGetName():"default");
                        }
                    }
                    break;
                case 'ext':
                    if(!isset($this->params['url']['tt'])){
                        $this->error();
                    }
                    //$tt is for category
                    $tt = $this->params['url']['tt'];
                    $ext = Configure::read('widget.ext');
                    if(!in_array($tt, array_keys($ext))){
                        $this->error();
                    }
                    foreach($ext[$tt][1] as $v){
                        try{
                            $w = Widget::getInstance($v);
                        }catch(WidgetNullException $e){
                            continue;
                        }
                        if(!in_array($w->wGetName(), $my)){
                            $title = $w->wGetTitle();
                            $title = $title["text"];
                            $ret[] = array('wid' => $w->wGetName(), 'title' => $title, "p"=>file_exists(IMAGES . 'app/icon/'.$w->wGetName().'.png')?$w->wGetName():"default");
                        }
                    }
                    break;
                case 'search':
                    if(!isset($this->params['url']['tt'])){
                        $this->error();
                    }
                    //$tt is for widget name
                    $tt = urldecode(urldecode($this->params['url']['tt']));
                    $tt = nforum_iconv('utf-8', $this->encoding, $tt);
                    $ext = Configure::read('widget.ext');
                    foreach($ext as $v){
                        foreach($v[1] as $wid){
                            try{
                                $w = Widget::getInstance($wid);
                            }catch(WidgetNullException $e){
                                continue;
                            }
                            $title = $w->wGetTitle();
                            $title = $title["text"];
                            if(!in_array($w->wGetName(), $my) && strpos($title, $tt) !== false)
                                $ret[] = array('wid' =>$w->wGetName(), 'title' => $title, "p"=>file_exists(IMAGES . 'app/icon/'.$w->wGetName().'.png')?$w->wGetName():"default");
                        }
                    }
                    break;
            }
        }catch(Exception $e){
            $this->error();
        }
        $this->set('no_html_data',$ret);
        //no ajax status info
        $this->set('no_ajax_info', true);
    }
}
?>
