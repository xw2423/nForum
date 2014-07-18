<?php
load('model/blacklist');
class BlacklistController extends NF_ApiController {

    protected $_method = array('post' => array('add', 'delete'));

    public function init(){
        parent::init();
        $this->requestLogin();
    }

    public function listAction(){
        $data = array();
        $wrapper = Wrapper::getInstance();
        try{
            $bl = new Blacklist(User::getInstance());

            load('inc/pagination');
            $count = isset($this->params['url']['count'])?$this->params['url']['count']:c("pagination.friend");
            $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
            if(($count = intval($count)) <= 0)
                $count = c("pagination.friend");
            if($count > c('modules.api.page_item_limit'))
                $count = c("pagination.friend");
            $page = intval($page);

            $pagination = new Pagination($bl, $count);
            $data['user'] = array();;
            foreach($pagination->getPage($page) as $v){
                try{
                    $data['user'][] = $wrapper->user(User::getInstance($v['ID'], false));
                }catch(Exception $e){
                    $data['user'][] = $v['ID'];
                }
            }

            $data['pagination'] = $wrapper->page($pagination);
        }catch(BlacklistNullException $e){
            $this->error($e->getMessage());
        }

        $this->set('data', $data);
    }

    public function addAction(){
        if(!isset($this->params['form']['id']))
            $this->error();
        try{
            Blacklist::add($this->params['form']['id']);
        }catch(BlacklistAddException $e){
            $this->error($e->getMessage());
        }

        $this->set('data', array('status'=>true));
    }

    public function deleteAction(){
        if(!isset($this->params['form']['id']))
            $this->error();
        try{
            Blacklist::delete($this->params['form']['id']);
        }catch(BlacklistDeleteException $e){
            $this->error($e->getMessage());
        }

        $this->set('data', array('status'=>true));
    }
}
