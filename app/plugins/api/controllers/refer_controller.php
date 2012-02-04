<?php
App::import("vendor", array("model/refer"));
class ReferController extends ApiAppController {

    public function beforeFilter(){
        parent::beforeFilter();
        if(!Configure::read('refer.enable'))
            $this->error(ECode::$REFER_DISABLED);
        $this->requestLogin();
    }

    public function index(){
        if(!isset($this->params['type']))
            $this->error(ECode::$REFER_NONE);
        $type = $this->params['type'];
        try{
            $refer = new Refer(User::getInstance(), $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }

        $count = isset($this->params['url']['count'])?$this->params['url']['count']:Configure::read("pagination.mail");
        if(($count = intval($count)) <= 0)
            $count = Configure::read('pagination.mail');
        if($count > Configure::read('plugins.api.page_item_limit'))
            $count = Configure::read('pagination.mail');
        $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
        $page = intval($page);
        $pagination = new Pagination($refer, $count);
        $articles = $pagination->getPage($page);

        $wrapper = Wrapper::getInstance();
        $info = array();
        foreach($articles as $v){
            $info[] = $wrapper->refer($v);
        }
        $data['description'] = $refer->getDesc();
        $data['pagination'] = $wrapper->page($pagination);
        $data['article'] = $info;
        $this->set('data', $data);
    }

    public function setRead(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['type']))
            $this->error(ECode::$REFER_NONE);
        $type = $this->params['type'];
        try{
            $refer = new Refer(User::getInstance(), $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }

        if(!isset($this->params['index'])){
            $refer->setRead();
            $this->set('data', array('status' => true));
        }else{
            $wrapper = Wrapper::getInstance();
            $index = intval($this->params['index']);
            $refer->setRead($index);
            $this->set('data', $wrapper->refer($refer->getRefer($index)));
        }
    }

    public function delete(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['type']))
            $this->error(ECode::$REFER_NONE);

        $type = $this->params['type'];
        try{
            $refer = new Refer(User::getInstance(), $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }

        if(!isset($this->params['index'])){
            $refer->delete();
            $this->set('data', array('status' => true));
        }else{
            $wrapper = Wrapper::getInstance();
            $index = intval($this->params['index']);
            $this->set('data', $wrapper->refer($refer->getRefer($index)));
            $refer->delete($index);
        }
    }

    public function info(){
        if(!isset($this->params['type']))
            $this->error(ECode::$REFER_NONE);

        $type = $this->params['type'];
        $u = User::getInstance();
        try{
            $refer = new Refer($u, $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }
        $data['enable'] = ($u->getCustom('userdefine1', ($type == Refer::$AT)?2:3) == 1);
        $data['new_count'] = $refer->getNewNum();
        $wrapper = Wrapper::getInstance();
        $this->set('data', $data);
    }
}
?>
