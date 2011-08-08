<?php
App::import("vendor", array("model/friend"));
class FriendController extends AppController {

    public function beforeFilter(){
        parent::beforeFilter();
        $this->_init();
    }
    
    public function index(){
        $this->js[] = "forum.friend.js";
        $this->css[] = "mail.css";
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;
        try{
            $f = new Friend(User::getInstance());        
            App::import("vendor", "inc/pagination");
            $pagination = new Pagination($f, Configure::read("pagination.friend"));
            $friends = $pagination->getPage($p);
        }catch(FriendNullException $e){
            $this->error();
        }
        if($f->getTotalNum() > 0){
            $info = array();
            foreach($friends as $v){
                $info[] = array(
                    "fid" => $v->userid,
                    "desc" => $v->exp
                );
            }
            $this->set("friends", $info);
        }
        $link = "?p=%page%";
        $pageBar = $pagination->getPageBar($p, $link);
        $this->set("pageBar", $pageBar);
        $this->set("totalNum", $f->getTotalNum());
        $this->set("curPage", $pagination->getCurPage());
        $this->set("totalPage", $pagination->getTotalPage());
    }

    public function online(){
        $this->js[] = "forum.friend.js";
        $this->css[] = "mail.css";

        App::import('Sanitize');
        $u = User::getInstance();
        $online = $u->getOnlineFriends();
        if(count($online) > 0){
            foreach($online as $v){
                $info[] = array(
                    "fid" => $v->userid,
                    "from" => $v->userfrom,
                    "mode" => $v->mode,
                    "idle" => sprintf('%02d:%02d',intval($v->idle/60), ($v->idle%60))
                );
            }
            $this->set("friends", $info);
        }
    }

    public function add(){
        if(!isset($this->params['url']['id']))
            $this->error(ECode::USER_NOID);
        $id = $this->params['url']['id'];
        try{
            $ret = Friend::add($id);
        }catch(FriendAddException $e){
            $this->error($e->getMessage());
        }
        $this->waitDirect(
            array(
                "text" => "好友列表", 
                "url" => "/friend"
            ), ECode::$FRIEND_ADDOK);
    }

    public function delete(){
        foreach($this->params['form'] as $k=>$v){
            if(!preg_match("/f_/", $k))
                continue;
            $id = split("_", $k);
            try{
                $ret = Friend::delete($id[1]);
            }catch(Exception $e){
                continue;
            }
        }
        $this->waitDirect(
            array(
                "text" => "好友列表", 
                "url" => "/friend"
            ), ECode::$FRIEND_DELETEOK);
    }

    private function _init(){
        $this->requestLogin();
        $this->notice[] = array("url"=>"/friend", "text"=>"好友列表");
    }

}
?>
