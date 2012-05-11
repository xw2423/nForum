<?php
App::import("vendor", array("model/friend"));
class FriendController extends AppController {

    public function beforeFilter(){
        parent::beforeFilter();
        $this->requestLogin();
        $this->notice[] = array("url"=>"/friend", "text"=>"好友列表");
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
        $link = "{$this->base}/friend?p=%page%";
        $this->set("pageBar", $pagination->getPageBar($p, $link));
        $this->set("pagination", $pagination);
    }

    public function online(){
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

    public function ajax_list(){
        $f = new Friend(User::getInstance());
        $friends = $f->getRecord(1, $f->getTotalNum());
        $ret = array();
        foreach($friends as $v){
            $ret[] = $v->userid;
        }
        $this->set('no_html_data', $ret);
        $this->set('no_ajax_info', true);
    }

    public function ajax_add(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        if(isset($this->params['form']['id']))
            $id = $this->params['form']['id'];
        else if(isset($this->params['url']['id']))
            $id = $this->params['url']['id'];
        else
            $this->error(ECode::$USER_NOID);
        try{
            Friend::add($id);
        }catch(FriendAddException $e){
            $this->error($e->getMessage());
        }
        $ret['ajax_code'] = ECode::$FRIEND_ADDOK;
        $ret['default'] = "/friend";
        $ret['list'][] = array("text" => '好友列表',"url" => "/friend");
        $this->set('no_html_data', $ret);
    }

    public function ajax_delete(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        foreach($this->params['form'] as $k=>$v){
            if(!preg_match("/f_/", $k))
                continue;
            $id = split("_", $k);
            try{
                Friend::delete($id[1]);
            }catch(Exception $e){
                continue;
            }
        }
        $ret['ajax_code'] = ECode::$FRIEND_DELETEOK;
        $ret['default'] = "/friend";
        $ret['list'][] = array("text" => '好友列表',"url" => "/friend");
        $this->set('no_html_data', $ret);
    }
}
?>
