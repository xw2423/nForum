<?php
/**
 * ip acl component for nforum 
 * @author xw       
 */
App::import("vendor", "inc/iplib");
class IpAclComponent extends Object {    

    private $_active = false;
    public $components = array('ByrSession');
    
    //called before Controller::beforeFilter()
    public function initialize(&$controller, $settings = array()) {
        $this->controller = $controller;
        if(Configure::read("ipacl.on")){
            Configure::load("ipacl");
            $this->_active = true;
        }
    }

    //called after Controller::beforeFilter()
    public function startup(&$controller) {
        if(!$this->_active)
            return;
        if(!$this->check($this->ByrSession->from, Configure::read("ipacl.global")))
            $this->controller->error(ECode::$SYS_IPBAN);
        if(!$this->check($this->ByrSession->from, Configure::read("ipacl.{$this->controller->params['controller']}.{$this->controller->params['action']}")))
            $this->controller->error(ECode::$SYS_IPBAN);
    }

    //true for allow false for deny
    public function check($ip, $list){
        $v4 = !nforum_is_ipv6($ip);
        foreach((array)$list as $v){
            $tv4 = (strpos($v[0], ':') === false);
            if($v4 && $tv4){
                if(mask_equal(ip2long($ip), ip2long($v[0]), $v[1]))
                    return $v[2];
            }else if(!$v4 && !$tv4){
                $arr1 = ipv62long($ip);
                $arr2 = ipv62long($v[0]);
                if($v[1] > MASK_NUM_V6){
                    if(!mask_equal_v6($arr1[1], $arr2[1], $v[1] - MASK_NUM_V6))
                        continue;
                }
                $mask = ($v[1] > MASK_NUM_V6)?MASK_NUM_V6:$v[1];
                if(mask_equal_v6($arr1[0], $arr2[0], $mask))
                    return $v[2];    
            }
        }
        return true;
    }
}
?>
