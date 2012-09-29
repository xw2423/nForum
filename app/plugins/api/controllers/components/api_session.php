<?php
class ApiSessionComponent extends Object{

    public $isLogin = false;
    public $from = "";
    private $_expire = 3600;

    public function initialize(&$controller) {
        $this->controller = $controller;
    }

    public function initLogin(){
        App::import('vendor', array('db', 'api.basic_auth'));
        $id = BasicAuth::getCurrentUser();
        if(false === $id)
            $this->controller->error(ECode::$LOGIN_ERROR);
        $this->isLogin = ($id !== 'guest');
        if('guest' !== $id){
            $ret = Forum::checkBanIP($id, $this->from);
            switch($ret){
                case 1:
                    $this->controller->error(ECode::$LOGIN_IPBAN);
                    break;
                case 2:
                    $this->controller->error(ECode::$LOGIN_EPOS);
                    break;
                case 3:
                    $this->controller->error(ECode::$LOGIN_ERROR);
                    break;
            }
        }

        $db = DB::getInstance();
        if($u = $db->one('select id, utmpnum, utmpkey from pl_api_session where id=?', array($id))){
            if(Forum::initUser($u['id'],intval($u['utmpnum']),intval($u['utmpkey']))){
                $val = array('expire' => (time() + $this->_expire));
                $db->update('pl_api_session', $val, 'where id=?', array($u['id']));
                return;
            }
        }
        $ret = Forum::setUser(true);
        switch($ret){
            case -1:
                $this->controller->error(ECode::$LOGIN_MULLOGIN);
            case 1:
                $this->controller->error(ECode::$LOGIN_MAX);
            case 3:
                $this->controller->error(ECode::$LOGIN_IDBAN);
            case 4:
                $this->controller->error(ECode::$LOGIN_IPBAN);
            case 5:
                $this->controller->error(ECode::$LOGIN_FREQUENT);
            case 7:
                $this->controller->error(ECode::$LOGIN_NOPOS);
        }
        User::update();
        $user = User::getInstance();
        if($u){
            $val = array('utmpnum' => $user->index, 'utmpkey' => $user->utmpkey, 'expire' => time() + $this->_expire);
            $db->update('pl_api_session', $val, 'where id=?', array($user->userid));
        }else{
            $val = array('k'=>array('id', 'utmpnum', 'utmpkey', 'expire'), 'v'=>array(array($user->userid, $user->index, $user->utmpkey, time() + $this->_expire)));
            $db->insert('pl_api_session', $val);
        }
    }

    public function setFromHost(){
        @$this->from = $_SERVER["REMOTE_ADDR"];
        if(Configure::read('proxy.X_FORWARDED_FOR')){
            @$fullfrom = $_SERVER["HTTP_X_FORWARDED_FOR"];
            if($fullfrom != ""){
                $ips = explode(",", $fullfrom);
                $this->from = array_pop($ips);
            }
        }
        if($this->from == "")
            $this->from = "127.0.0.1";
        Forum::setFrom($this->from, "");
    }

    public function logout(){
        $db = DB::getInstance();
        $user = User::getInstance();
        $db->delete('pl_api_session', 'where id=?', array($user->userid));
        Forum::kickUser();
    }
}
?>
