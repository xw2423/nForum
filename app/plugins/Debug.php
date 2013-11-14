<?php
class DebugPlugin extends Yaf_Plugin_Abstract{
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
    }
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
        if($request->html && !$request->front){
            $d = Yaf_Application::app()->getDispatcher();
            $r = $d->getRouter();
            dump($r->getRoute($r->getCurrentRoute()), $request, 'base:'.$request->getBaseUri(), 'request:'.$request->getRequestUri());
        }
    }
    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
    }
    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
    }
    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
    }
    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
    }
}
