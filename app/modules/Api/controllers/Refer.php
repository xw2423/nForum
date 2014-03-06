<?php
load('model/refer');
class ReferController extends NF_ApiController {

    protected $_method = array('post' => array('setread', 'delete'));

    public function init(){
        parent::init();
        if(!c('refer.enable'))
            $this->error(ECode::$REFER_DISABLED);
        $this->requestLogin();
    }

    public function indexAction(){
        if(!isset($this->params['type']))
            $this->error(ECode::$REFER_NONE);
        $type = $this->params['type'];
        try{
            $refer = new Refer(User::getInstance(), $type);
        }catch(ReferNullException $e){
            $this->error(ECode::$REFER_NONE);
        }

        $count = isset($this->params['url']['count'])?$this->params['url']['count']:c("pagination.mail");
        if(($count = intval($count)) <= 0)
            $count = c('pagination.mail');
        if($count > c('modules.api.page_item_limit'))
            $count = c('pagination.mail');
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

    public function setReadAction(){
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

    public function deleteAction(){
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

    public function infoAction(){
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
