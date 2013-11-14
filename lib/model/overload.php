<?php
/**
 * abstract object for magic method
 * to visit the properties from kbs
 *
 * @author xw
 */

abstract class OverloadObject {
    protected $_info = array();

    public function __get($name){
        $val = null;
        if(array_key_exists($name, $this->_info))
            $val = $this->_info["$name"];
        return $val;
    }

    public function __set($name, $val){}

    public function __isset($name){
        return array_key_exists($name, $this->_info);
    }

    public function __unset($name){}

}
