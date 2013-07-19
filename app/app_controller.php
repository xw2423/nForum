<?php
/**
 * Application controller for nforum
 * all of controllors extend from it
 *
 * @author xw
 */

//import user and code default
App::import("vendor", array("model/forum", "model/user", "model/code"));
class AppController extends Controller {


    //default components for all the controller
    public $components = array('RequestHandler', 'IpAcl', 'UaAcl');

    public $uses = null;

    //use javascript tmpl & show header,footer,left or not
    public $front = false;
    //for spider seo
    public $spider = false;

    //js & css for controller
    public $js = array();
    public $css = array();
    //js for run
    public $jsr = array();

    //normal for nav,special for notice
    public $notice = array();

    //title
    public $title = "";

    //debug array
    public $dp = array();

    //app encoding,it may be not App.encoding in plugin
    public $encoding;
    //value of App.encoding, it is also kbs encoding, gbk default
    public $appEncoding;

    //path
    public $path;

    public $view = null;

    //is html render or not(other is json,xml....)
    public $html = true;

    private $_hasRendered = false;

    public function __construct(){
        parent::__construct();
        //add ByrSession & RedirectAcl components
        $this->components[] = 'ByrSession';
        $this->components[] = 'RedirectAcl';
        $this->_kbsExtInstall();
        $this->appEncoding = $this->encoding = Configure::read("App.encoding");
    }

    /**
     * @override method
     */
    public function beforeFilter(){
        $this->RequestHandler->enabled = false;
        $this->path = str_replace($this->base, "", $this->here);
        $this->base = Configure::read('site.prefix');
        $this->_initAjax();
        $this->_initPlugin();

        $this->ByrSession->setFromHost();
        $this->ByrSession->initLogin();
        $this->ByrSession->setCookie();
    }

    /**
     * @override method
     */
    public function afterFilter(){
        if($this->RequestHandler->isFlash())
            $this->output = nforum_iconv($this->encoding, 'utf-8', $this->output);

        if($this->html){
            if($this->spider){
                if(!$this->front)
                    $this->output = $this->view->render('header', '') . $this->output . $this->view->render('footer', '');
            }else if(!$this->front)
                $this->output = $this->view->render('css', '') . $this->output . $this->view->render('script', '');
        }

        if(Configure::read() >= 2)
            $this->output .= $this->_getDump();
        if(Configure::read() == 3)
            $this->output .= $this->_getDebug();
        if(Configure::read() == 3)
            $this->output .= $this->_getDebug();
    }

    /**
     * @override method
     */
    public function beforeRender(){
        if(!$this->html){
            //there is three ajax_st status
            //1:success
            //0:fail
            $data = $this->get('no_html_data');
            if(null === $data)
                $data = array();
            //use no_ajax_info param to ignore ajax info,
            //sometimes data may be a array
            if(!isset($data['ajax_st']) && null === $this->get('no_ajax_info')){
                $data['ajax_st'] = 1;
                $data['ajax_code'] = isset($data['ajax_code'])?$data['ajax_code']:ECode::$SYS_AJAXOK;
                $data['ajax_msg'] = ECode::msg($data['ajax_code']);
                //code may be a string,check msg == code to set default code
                if($data['ajax_msg']  == strval($data['ajax_code']))
                    $data['ajax_code'] = ECode::$SYS_AJAXOK;
                $this->set('no_html_data', $data);
            }
            return;
        }

        $site = Configure::read("site");
        try{
            $u = User::getInstance();
            $uid = $u->userid;
            $admin = $u->isAdmin();
            $reg = $u->isReg();
        }catch(UserNullException $e){
            $uid = 'guest';
            $admin = false;
            $reg = false;
        }

        //handle js & css
        $this->_initAsset();
        //add pack js&css
        $asset_pack = nforum_cache_read('asset_pack');
        if(is_array($asset_pack) && ($this->front || $this->spider)){
            $this->set('js' ,array_merge(array('js/' . $asset_pack['js']), $this->get('js')));
            $this->set('css' ,array_merge(array('css/' . $asset_pack['css']), $this->get('css')));
        }

        //handle title
        $title = $site['name'];
        if(!empty($this->title)){
            $title = $this->title;
        }else if(isset($this->notice[0]) &&  $this->path != $site['home']){
            $title .= "-" . $this->notice[0]["text"];
        }else{
            $title .= "-" . $site['desc'];
        }

        //handle notice
        if($this->path != $site['home'])
            $this->notice= array_merge(array(array("url" => $site['home'], "text" => $site['name'])), $this->notice);

        /* handle jsr start*/
        if(!$this->spider){
            if($this->front){
                $site = Configure::read("site");
                $cookie = Configure::read("cookie");
                $jsr = Configure::read("jsr");
                $jsr['iframe'] = $jsr['iframe']?'true':'false';
                $jsr['domain'] = preg_replace('/^https?:\/\//', '', $site['domain']);
                $jsr['cookie_domain'] = $cookie['domain'];
                $jsr['base'] = $this->base;
                $jsr['prefix'] = $cookie['prefix'];
                $jsr['home'] = $site['home'];
                $jsr['static'] = $site['static'];
                $jsr['protocol'] = $site['ssl']?'https://':'http://';
                App::import("vendor", "inc/json");
                $jsr = 'var sys_merge=' . BYRJSON::encode($jsr);
                $this->jsr = array_merge(array($jsr), $this->jsr);
                $syn = Configure::read('ubb.syntax');
                if(Configure::read('ubb.parse') && !empty($syn)){
                    $this->set('js' ,array_merge($this->get('js'), array($syn . '/scripts/shCore.js', $syn . '/scripts/shAutoloader.js')));
                }
            }else{
                $tmp = array();
                foreach($this->notice as $v){
                    $tmp[] = '<a href="' . (empty($v['url'])?'javascript:void(0)':($this->base.$v['url'])) . '">' . $v['text'] . '</a>';
                }
                $tmp = join('&ensp;>>&ensp;', $tmp);
                $this->jsr[] = <<<EOT
$('#notice_nav').html('{$tmp}');$.setTitle('{$title}');
EOT;

                $syn = Configure::read('ubb.syntax');
                if(Configure::read('ubb.parse') && !empty($syn) && $this->get('hasSyn') !== false){
                    $this->set('syntax', $syn);
                }
            }
        }
        /* handle jsr end*/

        //basic variables
        $arr = array(
            'base' => $this->base
            ,'islogin' => $this->ByrSession->isLogin
            ,'id' => $uid
            ,'isAdmin' => $admin
            ,'isReg' => $reg
            ,'notice' => $this->notice
            ,'webTitle' => $title
            ,'webTotal' => Forum::getOnlineNum()
            ,'webUser' => Forum::getOnlineUserNum()
            ,'webGuest' => Forum::getOnlineGuestNum()
            ,'encoding' => $this->encoding
            ,'domain' => $site['domain']
            ,'static' => $site['static']
            ,'siteName' => $site['name']
            ,'keywords' => $site['keywords']
            ,'description' => $site['description']
            ,'home' => $site['home']
            ,'preindex' => $site['preIndex']
            ,'front' => $this->front
            ,'spider' => $this->spider
            ,'jsr' => $this->jsr
        );

        $this->set($arr);
    }

    /**
     * use AppView for render view
     * if viewVars has key 'data' using json or xml
     *
     * @param $action
     * @param $path with DS end
     * @override method
     */
    public function render($action = null, $path = null) {
        if($this->_hasRendered)
            return $this->output;
        $this->beforeRender();
        $this->Component->beforeRender($this);
        if(is_null($this->view)){
            App::import("vendor", "inc/view");
            try{
                $this->view = AppView::getInstance($this->params['url']['ext'], $this);
                $this->output .= $this->view->render($action, $path);
            }catch(AppViewException $e){
                $this->_stop();
            }
        }
        $this->_hasRendered = true;
        return $this->output;
    }

    /**
     * assing variable to templete
     * @param $one string or array
     * @param $two mixed
     */
    public function set($one, $two = null) {
        if(is_array($one) && is_null($two)){
            foreach($one as $k => $v){
                $this->viewVars[$k] = $v;
            }
        }
        if(is_string($one) && !is_null($two)){
            $this->viewVars[$one] = $two;
        }
    }

    /**
     * clear variable that has set
     * @param $one string or array
     */
    public function clear($one){
        if(is_array($one)){
            foreach($one as $k => $v){
                unset($this->viewVars[$k]);
            }
        }
        if(is_string($one)){
            unset($this->viewVars[$one]);
        }
    }

    /**
     * get variable of templete
     * @param $name string
     * @return value|null
     */
    public function get($name = null) {
        return isset($this->viewVars[$name])?$this->viewVars[$name]:null;
    }

    /**
     * add redirect for ajax page
     * using 'location:URL' as text
     * @param string $url
     * @param int $status
     * @param boolean $exit
     */
    public function redirect($url, $status = null, $exit = true){
        if(!$this->front && !preg_match("#^(https|http|ftp|rtsp|mms)://#i", $url)){
            echo "location:$url";
            $this->_stop();
        }
        parent::redirect($url, $status, $exit);
    }

    /**
     * wait and rediret to url with list for choose
     * @param array $url array("url"=>, "text"=>) if url is null go back
     * @param mixed $code
     * @param array $list array(array("url"=>, "text"=>), array())
     * @param array $args
     */
    public function waitDirect($url, $code, $list = array(), $args = array()){
        $msg = ECode::msg($code);
        if(!empty($args))
            $msg = vsprintf($msg, $args);
        $params = array( 'html' => $this->html
            ,'ajax_st' => 1
            ,'ajax_code' => $code
            ,"ajax_msg" => $msg
            ,"url" => $url
            ,"list" => $list
        );
        $this->cakeError("redirect", $params);
    }

    /**
     * redirect to the error page
     * handle normal request & ajax request
     * @param int $code
     * @param array $args
     * @param array $param
     */
    public function error($code = null, $args = array(), $other = array()){
        if(is_a($this, 'CakeErrorController'))
            return;
        if(is_null($code))
            $code = ECode::$SYS_AJAXERROR;
        $msg = ECode::msg($code);
        if($msg == strval($code))
            $code = ECode::$SYS_AJAXERROR;
        if(!empty($args))
            $msg = vsprintf($msg, $args);
        $params = array( 'html' => $this->html
            ,'ajax_st' => 0
            ,'ajax_code' => $code
            ,'ajax_msg' => $msg
        );
        if(!empty($other))
            $params = array_merge($params, $other);
        $this->cakeError("error", $params);
    }

    public function error404($code = null, $args = array(), $other = array()){
        if(is_a($this, 'CakeErrorController'))
            return;
        $msg = ECode::msg($code);
        if($msg == strval($code))
            $code = null;
        if(!empty($args))
            $msg = vsprintf($msg, $args);
        $this->cakeError("error404", array('html' => $this->html, 'code' => $code, 'msg' => $msg));
    }

    /**
     * make cache in respones
     * @param $switch cache or not
     * @param $modified
     * @param $expires
     */
    public function cache($switch = false, $modified = 0, $expires = null){
        if($switch){
            if(is_null($expires))
                $expires = Configure::read("cache.second");
            if(!is_int($modified))
                $modified = 0;
            @$oldmodified = $_SERVER["HTTP_IF_MODIFIED_SINCE"];
            if ($oldmodified != "") {
                if (($pos = strpos($oldmodified, ';')) !== false)
                    $oldmodified = substr($oldmodified, 0, $pos);
                $oldtime = strtotime($oldmodified);
            }else
                $oldtime = -1;
            if ($oldtime >= $modified){
                $this->header("HTTP/1.1 304 Not Modified");
                $this->header("Cache-Control: max-age=" . "$expires");
                $this->_stop();
            }
            $this->header("Last-Modified: " . gmdate("D, d M Y H:i:s", $modified) . " GMT");
            $this->header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expires) . " GMT");
            $this->header("Cache-Control: max-age=" . "$expires");
        }else{
            $this->header("Expires: Thu, 18 Feb 1988 01:00:00 GMT");
            $this->header("Cache-Control: no-store, no-cache, must-revalidate");
            $this->header("Pragma: no-cache");
        }
    }

    /**
     * check whehter login or redirect
     * @param $from
     */
    public function requestLogin($from = null){
        if(!$this->ByrSession->isLogin)
            $this->error(ECode::$SYS_NOLOGIN);

        //no cache if need login
        $this->cache(false);
    }

    /**
     * check it is ajax or not
     * if the action is with prefix 'ajax'
     * check xhr
     */
    protected function _initAjax(){
        if(0 === strpos($this->params['action'], 'ajax_')){
            $this->html = false;
            if(!$this->RequestHandler->isAjax() && Configure::read("ajax.check"))
                $this->error404();
            if(!$this->ByrSession->hasCookie && Configure::read("ajax.valid"))
                $this->error404();
        }
    }

    /**
     * kbs init
     */
    protected function _kbsExtInstall(){
        if (BUILD_PHP_EXTENSION==0)
            @dl("libphpbbslib.so");
        chdir(BBS_HOME);
        if (!bbs_ext_initialized())
                bbs_init_ext();
    }

    /**
     * get $dp content
     */
    protected function _getDump() {
        ob_start();
        foreach($this->dp as $v){
            echo '<div style="color:white;background-color:black;float:none">';
            debug($v);
            echo '</div><br />';
        }
        return ob_get_clean();
    }

    protected function _initPlugin(){
        if(empty($this->params['plugin']))
            return;
        if(!in_array(strtolower($this->params['plugin']), Configure::read("plugins.install")))
            $this->error(ECode::$SYS_PLUGINBAN);
    }

    protected function _initAsset(){
        App::import('vendor', 'inc/packer');
        $p = new Packer();
        $js_out = $css_out = '';
        $js_pack = Configure::read("Asset.filter.js");
        $css_pack = Configure::read("Asset.filter.css");

        /* handle js*/
        $js_filter = array('js/plupload.min.js');
        //get relative path with WWW_ROOT
        foreach($this->js as $k=>$v){
            if($k === "plugin"){
                foreach($v as $pl=>$_v){
                    foreach($_v as $js)
                        $this->js[] = "plugins/{$pl}/js/{$js}";
                }
                unset($this->js['plugin']);
            }else{
                $this->js[$k] = "js/$v";
            }
        }
        //check for direct output
        foreach($this->js as $k=>$v){
            if(!in_array($v, $js_filter)){
                $js_out .= $js_pack?$p->pack(WWW_ROOT . $v, 'js'):file_get_contents(WWW_ROOT . $v);
                unset($this->js[$k]);
            }
        }

        if($js_pack){
            foreach($this->js as &$js)
                $js = str_replace("js/", "cjs/", $js);
        }
        /* handle js end*/

        /* handle css*/
        $css_filter = array('css/xwidget.css');
        //get relative path with WWW_ROOT
        foreach($this->css as $k=>$v){
            if($k === "plugin"){
                foreach($v as $pl=>$_v){
                    foreach($_v as $css){
                        $this->css[] = "plugins/{$pl}/css/{$css}";
                        //filter plugin css
                        $css_filter[] = "plugins/{$pl}/css/{$css}";
                    }
                }
                unset($this->css['plugin']);
            }else{
                $this->css[$k] = "css/$v";
            }
        }
        /**
         * sometime css can output with html to reduce http request
         * but if css has image referrence, the image will hard to find
         * so I keep css file as a link
         */
        //check for direct output
        foreach($this->css as $k=>$v){
            if(!in_array($v, $css_filter)){
                $css_out .= $css_pack?$p->pack(WWW_ROOT . $v, 'css'):file_get_contents(WWW_ROOT . $v);
                unset($this->css[$k]);
            }
        }
        if($css_pack){
            foreach($this->css as &$css)
                $css = str_replace("css/", "ccss/", $css);
        }
        /* handle css end*/

        $this->set("js", $this->js);
        $this->set("js_out", $js_out);
        $this->set("css", $this->css);
        $this->set("css_out", $css_out);
    }

    /**
     * get $controller content
     */
    protected function _getDebug() {
        ob_start();
        echo '<div style="color:white;background-color:black">';
        debug($this);
        echo '</div>';
        return ob_get_clean();
    }
}
?>
