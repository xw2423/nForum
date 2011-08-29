<?php
App::import("vendor", array("model/favor"));
class FavorController extends MobileAppController {

    public function beforeFilter(){
        parent::beforeFilter();
        $this->requestLogin();
        if(!isset($this->params['num']))
            $this->params['num'] = 0;
    }

    public function index(){
        $this->cache(false);
        $this->notice = "ÊÕ²Ø¼Ð";
        App::import('Sanitize');
        $level = (int)$this->params['num'];
        try{
            $fav = Favor::getInstance($level);
        }catch(FavorNullException $e){
            $this->error(ECode::$USER_FAVERROR);
        }
        $p = $ret = false;
        if(!$fav->isNull()){
            $brds = $fav->getAll();
            foreach($brds as $k=>$v){
                $ret[$k]['name'] = $v->NAME;
                $ret[$k]['desc'] = $v->DESC;
                $ret[$k]['dir'] = $v->isDir()?1:0;
                $ret[$k]['pos'] = $v->NPOS;
                $ret[$k]['url'] = $v->isDir()?(($v->NAME == "")?("/favor/".$v->BID):("/section/".$v->NAME)):"/board/".$v->NAME;
            }
        }
        $parent = $fav->getParent();
        if($parent){
            $p = "/favor/" . $parent->getLevel();
        }
        $this->set("info", $ret);
        $this->set("parent", $p);
    }
}
?>
