<?php
load('model/blacklist');
class BlacklistController extends NF_Controller {

    public function init(){
        parent::init();
        $this->requestLogin();
        $this->notice[] = array("url"=>"/blacklist", "text"=>"黑名单");
    }

    public function indexAction(){
        $this->js[] = "forum.friend.js";
        $this->css[] = "mail.css";

        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
        try{
            $f = new Blacklist(User::getInstance());
            load("inc/pagination");
            $pagination = new Pagination($f, c("pagination.friend"));
            $bl = $pagination->getPage($p);
        }catch(BlacklistNullException $e){
            $this->error();
        }
        if($f->getTotalNum() > 0){
            $info = array();
            foreach($bl as $v){
                $info[] = array(
                    "bid" => $v['ID'],
                );
            }
            $this->set("blacklist", $info);
        }
        $link = "{$this->base}/blacklist?p=%page%";
        $this->set("pageBar", $pagination->getPageBar($p, $link));
        $this->set("pagination", $pagination);
    }

    public function ajax_addAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        if(isset($this->params['form']['id']))
            $id = $this->params['form']['id'];
        else if(isset($this->params['url']['id']))
            $id = $this->params['url']['id'];
        else
            $this->error(ECode::$USER_NOID);
        try{
            Blacklist::add($id);
        }catch(BlacklistAddException $e){
            $this->error($e->getMessage());
        }
        $ret['ajax_code'] = ECode::$BLACKLIST_ADDOK;
        $ret['default'] = "/blacklist";
        $ret['list'][] = array("text" => '黑名单',"url" => "/blacklist");
        $this->set('no_html_data', $ret);
    }

    public function ajax_deleteAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        foreach($this->params['form'] as $k=>$v){
            if(!preg_match("/f_/", $k))
                continue;
            $id = split("_", $k);
            try{
                Blacklist::delete($id[1]);
            }catch(Exception $e){
                continue;
            }
        }
        $ret['ajax_code'] = ECode::$BLACKLIST_DELETEOK;
        $ret['default'] = "/blacklist";
        $ret['list'][] = array("text" => '黑名单',"url" => "/blacklist");
        $this->set('no_html_data', $ret);
    }
}
