<?php
/**
 * user-agent acl component for nforum 
 * @author xw       
 */
class UaAclComponent extends Object {    

    private $_active = false;
    public function initialize(&$controller, $settings = array()) {
        $this->controller = $controller;    
        Configure::load('uaacl');
        if(Configure::read("uaacl.on")){
            $this->_active = true;
        }
    }

    public function startup(&$controller) {
        if(!$this->_active)
            return;
        $ua = @env("HTTP_USER_AGENT");
        if(!$this->check(Configure::read("uaacl.global"), $ua))
            $this->controller->error(ECode::$XW_JOKE);
        if(null !== $this->controller->params['plugin']){
            $acl = Configure::read("uaacl.{$this->controller->params['plugin']}.{$this->controller->params['controller']}");
            if(isset($acl[$this->controller->params['action']]))
                $acl = $acl[$this->controller->params['action']];
            else
                $acl = array();
        }else{
            $acl = Configure::read("uaacl.{$this->controller->params['controller']}.{$this->controller->params['action']}");
        }
        if(!$this->check($acl, $ua))
            $this->controller->error(ECode::$XW_JOKE);
    }

    //true for allow false for deny
    //default:true
    public function check($acl, $ua = null){
        $ua = (string)(is_null($ua)?env("HTTP_USER_AGENT"):$ua);
        foreach((array)$acl as $v){
            if(preg_match($v[0], $ua))
                return $v[1];
        }
        return true;
    }
}
?>
