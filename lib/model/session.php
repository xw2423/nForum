<?php
/**
 * core session for nforum
 *
 * @author xw
 */
class NF_CoreSession{

    public $uid = null;
    public $utmpnum = null;
    public $utmpkey = null;
    public $isLogin = false;
    public $from = "";
    public $telnet = false;

    //true when setonline ok
    protected $_isInit = false;

    /**
     * init via userid, utmpnum, utmpkey
     */
    public function init(){
        if(!$this->telnet && null === $this->uid || null === $this->utmpnum || null === $this->utmpkey)
            return false;
        if($this->uid !== 'guest' && !$this->telnet && Forum::checkBanIP($this->uid, $this->from) != 0)
            return false;
        if(!Forum::initUser($this->uid, intval($this->utmpnum), intval($this->utmpkey), $this->telnet))
            return false;
        $this->_isInit = true;
        $this->isLogin = $this->uid !== 'guest';
        return true;
    }

    public function initGuest(){
        if($this->_isInit)
            return;
        $ret = Forum::setUser(false);
        if($ret == 0 || $ret == 2){
            $this->uid = 'guest';
            $this->isLogin = false;
        }
        $u = User::getInstance();
        Forum::initUser('guest', $u->index, $u->utmpkey);
    }

    /**
     * function login
     * if call Forum::checkPwd before, $pwd can be null
     *
     * $param string $id
     * $param string $pwd
     * $param boolean $md5
     */
    public function login($id, $pwd = null, $md5 = true){
        if($this->isLogin) return;
        $ret = Forum::checkBanIP($id, $this->from);
        switch($ret){
            case 1:
                throw new LoginException(ECode::$LOGIN_IPBAN);
                break;
            case 2:
                throw new LoginException(ECode::$LOGIN_EPOS);
                break;
            case 3:
                throw new LoginException(ECode::$LOGIN_ERROR);
                break;
        }
        if (null !== $pwd && 'guest' !== $id && !Forum::checkPwd($id, $pwd, $md5, true))
            throw new LoginException(ECode::$LOGIN_ERROR);

        $ret = Forum::setUser(true);
        switch($ret){
            case -1:
                throw new LoginException(ECode::$LOGIN_MULLOGIN);
            case 1:
                throw new LoginException(ECode::$LOGIN_MAX);
            case 3:
                throw new LoginException(ECode::$LOGIN_IDBAN);
            case 4:
                throw new LoginException(ECode::$LOGIN_IPBAN);
            case 5:
                throw new LoginException(ECode::$LOGIN_FREQUENT);
            case 7:
                throw new LoginException(ECode::$LOGIN_NOPOS);
        }
        User::update();
        $u = User::getInstance();
        $this->uid = $u->userid;
        $this->utmpnum = $u->index;
        $this->utmpkey = $u->utmpkey;
        $this->isLogin = true;
    }

    public function logout(){
        if($this->isLogin);
            Forum::kickUser();
        $this->isLogin = false;
    }

    protected function __construct(){
        $this->from = ip();
        if($this->from == '') $this->from = "127.0.0.1";

        //param 2 is unused
        Forum::setFrom($this->from, "");
    }
}

class NF_Session extends NF_CoreSession{
    private static $_instance = null;

    private $_pwd = null;

    //false when check from pwd
    private $_updateID = true;
    //use sid login
    private $_sid = false;

    public static function getInstance(){
        if(null === self::$_instance)
            self::$_instance = new NF_Session();
        return self::$_instance;
    }

    public function init($sid = null){
        $pwd = null;

        //get uid, utmpnum, utmpkey from sid or cookie
        if(null !== $sid){
            $this->setSession($sid);
        }else if(false === $this->_sid){
            $cookie = Cookie::getInstance();
            $this->utmpkey = $cookie->read("UTMPKEY", c('cookie.encryption'));
            $this->utmpnum = $cookie->read("UTMPNUM", c('cookie.encryption'));
            $this->uid = $cookie->read("UTMPUSERID");
            $pwd = $cookie->read("PASSWORD");
        }

        //check uid, utmpnum, utmpkey
        if(!parent::init()){
            //if failed check pwd
            if($this->uid !== 'guest'
                && $pwd && Forum::checkPwd($this->uid, base64_decode($pwd), true, true)){
                $ret = Forum::setUser(true);
                if($ret == 0 || $ret == 2){
                    $this->isLogin = true;
                    $this->_updateID = false;
                }else if($ret == 5){
                    throw new LoginException(ECode::$LOGIN_FREQUENT);
                }
            }else{
                $this->initGuest();
            }
        }

        //setcookie
        if(!$this->_isInit) $this->setCookie();
    }

    public function login($id, $pwd = null, $md5 = true, $cookieTime = null){
        parent::login($id, $pwd, $md5);
        if(null !== $pwd)
            $this->setCookie($pwd, $cookieTime);
    }

    public function logout(){
        parent::logout();

        $cookie = Cookie::getInstance();
        $cookie->delete('UTMPUSERID');
        $cookie->delete('UTMPKEY');
        $cookie->delete('UTMPNUM');
        $cookie->delete('PASSWORD');
    }

    public function setCookie($pwd = null, $cookieTime = null){
        $u = User::getInstance();
        $cookie = Cookie::getInstance();
        if($this->_updateID)
            $cookie->write('UTMPUSERID', $u->userid, false, $cookieTime);
        $cookie->write('UTMPKEY', $u->utmpkey, c('cookie.encryption'), $cookieTime);
        $cookie->write('UTMPNUM', $u->index);
        if(null !== $pwd)
            $cookie->write('PASSWORD', base64_encode($u->md5passwd), c('cookie.encryption'), $cookieTime);
    }

    public function setSession($sid){
        $this->_sid = $sid;
        $this->utmpnum = $this->_decodesessionchar($this->_sid[0])
            + $this->_decodesessionchar($this->_sid[1]) * 36
            + $this->_decodesessionchar($this->_sid[2]) * 36 * 36;
        $this->utmpkey = $this->_decodesessionchar($this->_sid[3])
            + $this->_decodesessionchar($this->_sid[4]) * 36
            + $this->_decodesessionchar($this->_sid[5]) * 36 * 36
            + $this->_decodesessionchar($this->_sid[6]) * 36 *36 * 36
            + $this->_decodesessionchar($this->_sid[7]) * 36 * 36 * 36 * 36
            + $this->_decodesessionchar($this->_sid[8]) * 36 * 36 * 36 * 36 * 36;
        $this->uid = null;
        $this->telnet = true;
    }

    public function getSession(){
        return $this->_sid;
    }

    protected function __construct(){
        parent::__construct();
        load('inc/cookie');
    }

    private function _decodesessionchar($ch){
        return strpos('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',$ch);
    }
}
class LoginException extends Exception{}
