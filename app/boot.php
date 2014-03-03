<?php
class Bootstrap extends Yaf_Bootstrap_Abstract{

    public function _initConfig($dispatcher){
        $app = Yaf_Application::app();
        define('APP', $app->getAppDirectory());
        define('MODULE', APP . DS . 'modules');
        define('VIEW', APP . DS . 'views');
        define('TMP', APP . DS . 'tmp');
        define('CACHE', TMP . DS . 'cache');
    }

    public function _initRequest($dispatcher){
        $request = $dispatcher->getRequest();

        //use javascript tmpl & show header,footer,left or not
        $request->front = false;

        //for spider seo
        $request->spider = false;

        //is html render or not(other is json,xml....)
        $request->html = true;

        $uri = $request->getRequestUri();
        $ext = strrchr($uri, '.');
        if('.json' === $ext || '.xml' === $ext)
            $uri = substr($uri, 0, strrpos($uri, $ext) - strlen($uri));
        else
            $ext = '.html';
        $request->ext= substr($ext, 1);

        //fix uri without ext
        $request->setRequestUri($uri);

        //url is the path relative to base
        $request->url = preg_replace('#^' . $request->getBaseUri() . '#', '', $uri);
        $request->front = ($request->url === '/');

        if(strpos($request->url, '/ccss/') === 0 || strpos($request->url, '/cjs/') === 0)
            nforum_compress_asset($request);

        //if use site.static, stop while file can't find
        if(c('site.isStatic')) nforum_error404(true);
    }

    public function _initRoute($dispatcher){
        $routes = array();
        if($m = c('modules.domain_module')){
            load(MODULE . DS . ucfirst($m) . DS . 'route', $routes);
        }else{
            load(CONF . DS . 'route', $routes);

            foreach(c('modules.install') as $v){
                $tmp = array();
                load(MODULE . DS . ucfirst($v) . DS . 'route', $tmp);
                $routes = array_merge($routes, $tmp);
            }
        }

        load('inc/route');
        $router = $dispatcher->getRouter();
        foreach(array_reverse($routes) as $k=>$r){
            if(empty($r[0])) continue;
            if(isset($r[3]))
                $router->addRoute($k, new NF_Route($r[0], $r[1], $r[2], $r[3]));
            else if(isset($r[2]))
                $router->addRoute($k, new NF_Route($r[0], $r[1], $r[2]));
            else if(isset($r[1]))
                $router->addRoute($k, new NF_Route($r[0], $r[1]));
            else
                $router->addRoute($k, new NF_Route($r[0]));
        }
    }

    public function _initPlugin($dispatcher){
        $tmp = c('plugins.install');
        if(null !== c('modules.install.1'))
            array_splice($tmp, 0, 0, 'module');
        $tmp[] = 'redirect';
        foreach($tmp as $k=>&$v){
            if(trim($v) === '') continue;

            $pl = ucfirst($v) . 'Plugin';
            if(class_exists($pl))
                $dispatcher->registerPlugin(new $pl);
        }
    }

    public function _initSite($dispatcher){
        load(array('inc/controller', 'inc/view', 'model/code'));
        $dispatcher->setView(NF_View::getInstance($dispatcher->getRequest()));
    }
}
