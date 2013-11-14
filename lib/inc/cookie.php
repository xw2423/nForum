<?php
class Cookie{
    private static $_instance = null;

    private $_prefix = 'NFORUM';
    private $_domain = '';
    private $_path = '/';

    public static function getInstance($cfg = null){
        if(null === $cfg){
            if(null === self::$_instance){
                $cfg = c('cookie');
                self::$_instance = new Cookie($cfg);
            }
            return self::$_instance;
        }
        return new Cookie($cfg);
    }

    public function read($key = null, $decrypt = false){
        if(isset($_COOKIE[$this->_prefix])){
            if(null === $key)
                return $_COOKIE[$this->_prefix];
            if(!isset($_COOKIE[$this->_prefix][$key]))
                return null;
            if($decrypt)
                return $this->decrypt($_COOKIE[$this->_prefix][$key]);
            return $_COOKIE[$this->_prefix][$key];
        }
        return null;
    }

    public function write($key, $value, $encrypt = false, $expires = null){
        $now = time();
        if(null === $expires)
            $expires = 0;
        else if(is_int($expires) || is_numeric($expires))
            $expires = $now + intval($expires);
        else
            $expires = strtotime($expires, $now);

        if($encrypt) $value = $this->encrypt($value);

        setcookie($this->_prefix . '[' . $key . ']', $value, $expires, $this->_path, $this->_domain);
    }

    public function delete($key){
        $this->write($key, '', time() - 88218);
    }

    public function encrypt($var){
        return urlencode($this->_getKey(strlen($var)) ^ strval($var));
    }

    public function decrypt($var){
        return $this->_getKey(strlen(urldecode($var))) ^ urldecode(strval($var));
    }

    private function _getKey($len){
        $ip = ip();
        if(strpos($ip, ':') !== false)
            $ip = join(":", array_slice(explode(':', $ip, 5), 0, 4))."::1";
        $hash = sha1($ip);
        $key = substr($hash, 4, $len);
        return $key;
    }

    private function __construct($cfg){
        $this->_prefix = $cfg['prefix'];
        $this->_domain = $cfg['domain'];
        $this->_path = $cfg['path'];
        $this->_encryption = $cfg['encryption'];
    }
}
