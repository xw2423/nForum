<?php
/**
 * redirect plugin for nforum
 * nforum redirect
 *
 * @author xw
 */
class RedirectPlugin extends Yaf_Plugin_Abstract{

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
        if('Index' !== ($m = $request->getModuleName())){
            //if use domain, Index can't visit
            $d = c('modules.' . strtolower($m) . '.domain');
            if(!empty($d) && c('site.domain') != 'http://' . $d)
                nforum_error404(true);

            load(MODULE . DS . $request->getModuleName() . DS . 'lib' . DS . 'controller.php');
            if('Mobile' === $m || 'Api' === $m){
                if($request->isXmlHttpRequest())
                    nforum_error404(true);
                else
                    return;
            }
        }

        //check ajax_* action via xhr in header
        if(0 === strpos($request->getActionName(), 'ajax_')){
            $request->html = false;
            if(!$request->isXmlHttpRequest() && c('ajax.check') && $request->getPost('emulate_ajax') != 'true')
                nforum_error404(true);
        }else if($request->spider){
            //this is a cheat for spider to access default page when spider visit '/'
            if('forum' === strtolower($request->getControllerName())
                && 'front' === strtolower($request->getActionName()))
                $request->setActionName('index');
        }else{
            //normal
            $r = self::isRedirect($request);
            $ajax = $request->isXmlHttpRequest();

            //should redirect but no ajax,go front
            if($r && !$ajax){
                $url = '/#!' . substr($request->url, 1);
                $query = $request->getQuery();
                if(!empty($query)){
                    foreach($query as $k => &$v)
                        $v = $k . '=' . $v;
                    $url .= '?' . join('&', $query);
                }
                nforum_redirect($request->getBaseUri() . $url);
            }

            //should not redirect but stop,
            if(!$r && $ajax) exit();
        }
    }

    public static function isRedirect($request){
        $acl = array();
        load(CONF . DS . 'redirectacl', $acl);

        $m = strtolower($request->getModuleName());
        $c = strtolower($request->getControllerName());
        $a = strtolower($request->getActionName());
        $r = true;
        if('index' !== $m){
            if(isset($acl[$m])){
                if(false === $acl[$m])
                    $r = false;
                $acl = $acl[$m];
            }else{
                $acl = array();
            }
        }
        if($r && isset($acl[$c])){
            if(false === $acl[$c])
                $r = false;
            else if(isset($acl[$c][$a]) && false === $acl[$c][$a])
                $r = false;
        }
        return $r;
    }
}
