<?php
/**
 * user-agent acl component for nforum
 * @author xw
 */
class UaAclComponent extends Object {

    private $_active = false;
    public function initialize(&$controller, $settings = array()) {
        $this->controller = $controller;
        if(Configure::read("uaacl.on")){
            Configure::load('uaacl');
            $this->_active = true;
        }
    }

    public function startup(&$controller) {
        if(!$this->_active)
            return;
        $ua = @env("HTTP_USER_AGENT");
        $acl = Configure::read("uaacl");
        if(!$this->check($acl['global'], $ua))
            $this->controller->error404();
        $plugin = $this->controller->params['plugin'];
        $con = $this->controller->params['controller'];
        $action = $this->controller->params['action'];
        if(null !== $plugin && isset($acl[$plugin])){
            $acl = $acl[$plugin];
        }
        if(isset($acl[$con][$action])){
            $acl = $acl[$con][$action];
        }else if(isset($acl[$con])){
            $acl = $acl[$con];
        }else{
            $acl = array();
        }
        if(!$this->check($acl, $ua))
            $this->controller->error404();
    }

    //true for allow false for deny
    //default:true
    public function check($acl, $ua = null){
        $ua = (string)(is_null($ua)?env("HTTP_USER_AGENT"):$ua);
        foreach((array)$acl as $v){
            if(!is_string($v[0])) continue;
            if(preg_match($v[0], $ua))
                return $v[1];
        }
        return true;
    }
}
?>
