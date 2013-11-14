<?php
load('inc/pagination');
class OnlineUser implements Pageable{

    private $_num;

    public function __construct(){
        $this->_num = Forum::getOnlineUserNum();
    }

    public function getTotalNum(){
        return $this->_num;
    }

    public function getRecord($start, $num){
        $users = array();
        $ret = bbs_getonline_user_list($start, $num);
        if($ret == 0)
            return array();
        foreach($ret as $v){
            $info = array();
            if(bbs_getuser($v['userid'], $info) == 0){
                throw new UserNullException();
            }
            $users[] = new User($info, $v);
        }
        return $users;
    }
}
