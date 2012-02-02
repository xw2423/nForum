<?php
App::import("vendor", array("model/refer"));
class ReferController extends MobileAppController{

    public function beforeFilter(){
        parent::beforeFilter();
        $this->requestLogin();
    }

    public function index(){
        $this->js[] = "forum.refer.js";
        $this->css[] = "mail.css";

        $type = Refer::$AT;
        $pageBar = "";
        if(isset($this->params['type']))
            $type = $this->params['type'];

        try{
            $refer = new Refer(User::getInstance(), $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }
        $this->notice = 'ндублАпя-' . $refer->getDesc();
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;

        App::import('vendor', "inc/pagination");
        try{
            $pagination = new Pagination($refer, Configure::read("pagination.mail"));
            $articles = $pagination->getPage($p);
        }catch(Exception $e){
            $this->error(ECode::$REFER_NONE);
        }
        if($refer->getTotalNum() > 0){
            $info = array();
            App::import('Sanitize');
            foreach($articles as $v){
                $info[] = array(
                    "index" => $v['INDEX'],
                    "id" => $v['ID'],
                    "board" => $v['BOARD'],
                    "user" => $v['USER'],
                    "title" => Sanitize::html($v['TITLE']),
                    "time" => date("Y-m-d H:i:s", $v['TIME']),
                    "read" => ($v['FLAG'] === Refer::$FLAG_READ)
                );
            }
            $this->set("info", $info);
        }
        $this->set("type", $type);
        $this->set("totalNum", $pagination->getTotalNum());
        $this->set("curPage", $pagination->getCurPage());
        $this->set("totalPage", $pagination->getTotalPage());
    }

    public function read(){
        if(!isset($this->params['type']))
            $this->error(ECode::$REFER_NONE);
        if(!isset($this->params['url']['index']))
            $this->error(ECode::$REFER_NONE);

        $type = $this->params['type'];
        try{
            $refer = new Refer(User::getInstance(), $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }

        if('all' == $this->params['url']['index']){
            $refer->setRead();
            $this->redirect($this->_mbase . "/refer/". $type);
        }else{
            $index = intval($this->params['url']['index']);
            $r = $refer->getRefer($index);
            if(null !== $r){
                $refer->setRead($index);
                $this->redirect("{$this->_mbase}/article/{$r['BOARD']}/single/{$r['ID']}/0");
            }else
                $this->redirect($this->_mbase . "/single/". $type);
        }
    }

    public function delete(){
        if(!isset($this->params['type']))
            $this->error(ECode::$REFER_NONE);

        $type = $this->params['type'];
        try{
            $refer = new Refer(User::getInstance(), $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }
        $index = intval($this->params['url']['index']);
        $refer->delete(intval($index));
        $this->redirect($this->_mbase . "/refer/{$type}?m=" . ECode::$REFER_DELETEOK);
    }
}
?>
