<?php
load('model/session');
class NF_ApiSession extends NF_CoreSession{

    private static $_instance = null;

    public static function getInstance(){
        if(null === self::$_instance)
            self::$_instance = new NF_ApiSession();
        return self::$_instance;
    }

    public function init(){
        load(array('inc/db', 'api.basic_auth'));
        $this->uid = BasicAuth::getCurrentUser();
        if(false === $this->uid)
            throw new LoginException(ECode::$LOGIN_ERROR);
        $db = DB::getInstance();
        if($u = $db->one('select id,utmpnum,utmpkey,expire from pl_api_session where id=?', array($this->uid))){
            $this->uid = $u['id'];
            $this->utmpnum = $u['utmpnum'];
            $this->utmpkey = $u['utmpkey'];
            if(parent::init())
                return true;
        }

        try{
            //check pwd in BasicAuth, $pwd is null
            parent::login($this->uid);
        }catch(LoginException $e){
            sleep(1);
            if($u = $db->one('select id,utmpnum,utmpkey,expire from pl_api_session where id=?', array($this->uid))){
                $this->uid = $u['id'];
                $this->utmpnum = $u['utmpnum'];
                $this->utmpkey = $u['utmpkey'];
                if(parent::init())
                    return true;
            }
            throw $e;
        }

        if($u){
            $val = array('utmpnum' => $this->utmpnum, 'utmpkey' => $this->utmpkey, 'expire' => time());
            $db->update('pl_api_session', $val, 'where id=?', array($this->uid));
        }else{
            $val = array('id' => $this->uid, 'utmpnum' => $this->utmpnum, 'utmpkey' => $this->utmpkey, 'expire' => time());
            $db->insert('pl_api_session', $val);
        }
    }

    public function logout(){
        parent::logout();
        $db = DB::getInstance();
        $db->delete('pl_api_session', 'where id=?', array($this->uid));
    }
}
