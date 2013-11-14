<?php
/**
 * user-agent acl plugin for nforum
 * @author xw
 */
class UaaclPlugin extends Yaf_Plugin_Abstract{

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
        $acl = array();
        load(CONF . '/uaacl', $acl);

        $request->spider = !self::check($acl['spider']);

        if(!self::check($acl['global']))
            nforum_error404(true);

        $m = strtolower($request->getModuleName());
        $c = strtolower($request->getControllerName());
        $a = strtolower($request->getActionName());
        if('index' !== $m && isset($acl[$m])){
            $acl = $acl[$m];
        }
        if(isset($acl[$c][$a])){
            $acl = $acl[$c][$a];
        }else if(isset($acl[$c])){
            $acl = $acl[$c];
        }else{
            $acl = array();
        }
        if(!self::check($acl))
            nforum_error404(true);
    }

    //true for allow false for deny
    //default:true
    public static function check($acl, $ua = null){
        if(null === $ua)
            $ua = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
        foreach((array)$acl as $v){
            if(!is_string($v[0])) continue;
            if(preg_match($v[0], $ua))
                return $v[1];
        }
        return true;
    }
}
