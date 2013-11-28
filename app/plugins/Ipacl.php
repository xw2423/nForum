<?php
load('inc/iplib');

/**
 * ip acl plugin for nforum
 * @author xw
 */
class IpaclPlugin extends Yaf_Plugin_Abstract{
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
        $acl = array();
        load(CONF . DS . 'ipacl', $acl);

        if(!self::check($acl['global']))
            exit(Ecode::msg(ECode::$SYS_IPBAN));

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
            nforum_error(ECode::$SYS_IPBAN, $request->front);
    }

    //true for allow false for deny
    public static function check($list, $ip = null){
        $ip = (string)(is_null($ip)?ip():$ip);
        $v4 = !nforum_is_ipv6($ip);
        foreach((array)$list as $v){
            if(!isset($v[0]) || !is_string($v[0])) continue;
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
