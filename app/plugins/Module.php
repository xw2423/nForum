<?php
/**
 * Module plugin for nforum
 *
 * @author xw
 */
class ModulePlugin extends Yaf_Plugin_Abstract{
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
        if('Index' !== ($m = $request->getModuleName())){
            //if use domain, Index can't visit
            $d = c('modules.' . strtolower($m) . '.domain');
            if(!empty($d) && c('site.domain') != 'http://' . $d)
                nforum_error404(true);

            load(MODULE . DS . $request->getModuleName() . DS . 'lib' . DS . 'controller.php');
        }
    }
}
