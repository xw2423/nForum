<?php
/**
 * redirect acl component for nforum
 * @author xw
 */
class RedirectAclComponent extends Object {

    private $_isRedirect = null;

    // if [plugin][controller][action] = true
    // will not redirect to front
    public function initialize(&$controller, $settings = array()) {
        $this->controller = $controller;
        //do not care pre defined ajax action
        if(0 === strpos($controller->params['action'], 'ajax_'))
            return;
        Configure::load('redirectacl');
        $acl = Configure::read('redirectacl');

        //spider
        if(!$this->controller->UaAcl->check($acl['spider'])){
            $this->controller->spider = true;

            //this is a cheat for spider to access default page when spider visit '/'
            if('forum' === $controller->params['controller']
                && 'front' === $controller->params['action'])
                $controller->params['action'] = 'index';

            return;
        }

        //normal
        $r = $this->isRedirect();
        $ajax = $controller->RequestHandler->isAjax();

        //should redirect but no ajax,go front
        if($r && !$ajax)
            $this->front();

        //should not redirect but stop,
        if(!$r && $ajax)
            $controller->_stop();
    }

    //redirect to front
    public function front(){
        $p = $this->controller->params['url'];
        $url = '/#!' . $p['url'];
        if($p['ext'] != 'html')
            $url .= '.' . $p['ext'];
        unset($p['url']);
        unset($p['ext']);
        if(!empty($p)){
            foreach($p as $k => &$v)
                $v = $k . '=' . $v;
            $url .= '?' . join('&', $p);
        }
        $this->controller->front = true;
        $this->controller->redirect($url);
    }

    public function isRedirect(){
        if(null !== $this->_isRedirect)
            return $this->_isRedirect;
        $acl = Configure::read('redirectacl');
        $plugin = $this->controller->params['plugin'];
        $con = $this->controller->params['controller'];
        $action = $this->controller->params['action'];
        $r = true;
        if(null !== $plugin){
            if(isset($acl[$plugin])){
                if(true === $acl[$plugin])
                    $r = false;
                $acl = $acl[$plugin];
            }else{
                $acl = array();
            }
        }
        if($r && isset($acl[$con])){
            if(true === $acl[$con])
                $r = false;
            else if(isset($acl[$con][$action]) && true === $acl[$con][$action])
                $r = false;
        }
        return $this->_isRedirect = $r;
    }
}
?>
