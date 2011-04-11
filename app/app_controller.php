<?php
/**
 * Application controller for nforum
 * all of controllors extend from it
 * use Smarty for View
 * 
 * @author xw
 */

//import user and code default
App::import("vendor", array("model/forum", "model/user", "model/code"));
class AppController extends Controller {

    
    //default components for all the controller
    public $components = array('RequestHandler', 'ByrSession', 'Adv', 'IpAcl', 'UaAcl');
    
    public $uses = null;

    //show left or not
    public $brief = false;

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

    //encoding
    public $encoding;

    //path
    public $path;

    public $view = null;

    //ajax mode for check
    private $_chAjax = false;

    public function __construct(){
        parent::__construct();
        $this->_kbsExtInstall();
        $this->encoding = Configure::read("App.encoding");
    }

    /**
     * @override method
     */
    public function beforeFilter(){
        $this->RequestHandler->enabled = false;
        $this->path = str_replace($this->base, "", $this->here);
        $this->base = Configure::read('site.prefix');
        $this->_initPlugin();
        $this->ByrSession->setFromHost();
        $this->ByrSession->initLogin();
        $this->ByrSession->setCookie();
        $this->header("Content-Type:text/html;charset=" . $this->encoding);
    }

    /**
     * @override method
     */
    public function afterFilter(){
        if(Configure::read() >= 2)
            $this->output .= $this->_getDump();
        if(Configure::read() == 3)
            $this->output .= $this->_getDebug();
    }

    /**
     * @override method
     */
    public function beforeRender(){
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

        $this->_initLeft();
        //default js & css
        $this->js = array_merge(array("jquery-1.4.4.pack.js", "forum.common.js"), $this->js);
        $this->css = array_merge(array("common.css"), $this->css);
        //handle js & css 
        $this->_initAsset();

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

        /* handle jsr end*/
        $cookie = Configure::read("cookie.domain");
        $jsr = Configure::read("jsr");
        $jsr = "var config={domain:'{$cookie}',base:'{$this->base}',mWidth:{$jsr['mWidth']},iframe:'{$jsr['iframe']}',allowFrame:'{$jsr['allowFrame']}'},user_login=".($this->ByrSession->isLogin?"true":"false").",uid='". $uid . "';";
        $this->jsr = array_merge(array($jsr), $this->jsr);
        /* handle jsr end*/

        //basic variables
        $this->set("islogin", $this->ByrSession->isLogin);
        $this->set("id", $uid);
        $this->set("isAdmin", $admin);
        $this->set("isReg", $reg);
        $this->set("brief", $this->brief);
        $this->set("notice", $this->notice);
        $this->set("webTitle", $title);
        $this->set("webTotal", Forum::getOnlineNum());
        $this->set("webUser", Forum::getOnlineUserNum());
        $this->set("webGuest", Forum::getOnlineGuestNum());
        $this->set("serverTime", date("Y-m-d H:i"));
        $this->set("encoding", $this->encoding);
        $this->set("domain", $site['domain']);
        $this->set("static", $site['static']);
        $this->set("siteName", $site['name']);
        $this->set("keywords", $site['keywords']);
        $this->set("description", $site['description']);
        $this->set("home", $site['home']);
        $this->set("base", $this->base);
        $this->set("jsr", $this->jsr);
    }
    
    /**
     * use Smarty for render view
     * @param $action
     * @param $path with DS end
     * @override method
     */
    public function render($action = null, $path = null) {
        $this->beforeRender();
        $this->Component->beforeRender($this);
        App::import("vendor", "inc/view");
        if(is_null($this->view))
            $this->view = new SmartyView($this, Configure::read("smarty"));
        $this->output .= $this->view->render($action, $path);    
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
     * wait and rediret to url with list for choose
     * @param array $url array("url"=>, "text"=>) if url is null go back
     * @param mixed $code
     * @param array $list array(array("url"=>, "text"=>), array())
     */
    public function waitDirect($url, $code, $list = null){
        $time = Configure::read("redirect.wait");
        $params = array(
            "url" => $url,
            "msg" => ECode::msg($code),
            "list" => $list,
            "time" => $time
        );
        $this->cakeError("redirect", $params);
    }

    /**
     * ajax request ok
     * @param $code
     * @param $param value for return
     */
    public function success($code = null, $params = null){
        $this->initAjax();
        if(is_null($code)){
            $code = ECode::$SYS_AJAXOK;
        }
        $ret = array("st" => "success", "code" => $code, "msg" => ECode::msg($code), "v" => $params);
        App::import("vendor", "inc/json");
        echo BYRJSON::encode($ret);
        $this->_stop();
    }

    /**
     * redirect to the error page
     * handle normal request & ajax request
     * @param $code
     * @param $msg
     */
    public function error($code = null){
        if($this->RequestHandler->isAjax()){
            $this->initAjax();
            if(is_null($code)){
                $code = ECode::$SYS_AJAXERROR;
            }
            $ret = array("st" => "error", "code"=> $code, 
                    "msg" => ECode::msg($code));
            App::import("vendor", "inc/json");
            echo BYRJSON::encode($ret);
            $this->_stop();
        }else{
            if(is_null($code)){
                $code = ECode::$SYS_ERROR;
            }
            $time = Configure::read("redirect.error");
            $params = array(
                "msg" => ECode::msg($code),
                "time" => $time
            );
            $this->cakeError("error", $params);
        }
    } 
    
    /**
     * init ajax request 
     * check xhr and make gbk output
     */
    public function initAjax(){
        if($this->_chAjax)
            return;
        $ajax = $this->RequestHandler->isAjax();
        if(!$ajax && Configure::read("ajax.check"))
            $this->error(ECode::$XW_JOKE);
        if(!$this->ByrSession->hasCookie && Configure::read("ajax.valid"))
            $this->error(ECode::$XW_JOKE);
        if($ajax)
            $this->header("Content-Type:application/json;charset=" . $this->encoding);
        $this->autoRender = false;
        $this->_chAjax = true;
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
            $this->header("Expires: Tue, 18 Nov 1988 09:00:00 GMT");
            $this->header("Cache-Control: no-store, no-cache, must-revalidate");
            $this->header("Pragma: no-cache");
        }
    }
    
    /**
     * check whehter login or redirect
     * @param $from
     */
    public function requestLogin($from = null){
        if(!$this->ByrSession->isLogin){
            if($this->RequestHandler->isAjax())
                $this->error(ECode::$SYS_NOLOGIN);
            if(is_null($from)){
                $query = array();
                foreach($this->params['url'] as $k=>$v){
                    if($k == 'url')
                        continue;
                    $query[] = $k . '=' . $v;
                }
                if(empty($query)){
                    $from = $this->path;
                }else{
                    $query = join('&', $query);
                    $from = $this->path . '?'. $query;
                }
            }
            $this->redirect("/login?from=" . urlencode($from));
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

    /**
     * init left of nforum
     */
    protected function _initLeft() {
        if($this->brief)
            return;
        $this->js[] = "forum.left.js";
        $secs = Configure::read("section");
        $this->set("secs", $secs);
        $this->set("from", $this->path);
        if($this->ByrSession->isLogin){
            $newNum = 0;
            App::import("vendor", "model/mail");
            try{
                $box = new MailBox(User::getInstance(), MailBox::$IN);
                $newNum = $box->getNewNum();
            }catch(MailBoxNullException $e){
            }catch(UserNullException $e){
            }
            $this->set("newNum", $newNum);
        }
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

        /* handle js*/
        $js_filter = array('js/jquery-1.4.4.pack.js', 'js/jquery-ui-1.8.7.pack.js', 'js/forum.common.js');
        $tmp = array();
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
                $js_out .= $p->pack(WWW_ROOT . $v, 'js');
                unset($this->js[$k]);
            }
        }
        if(Configure::read("Asset.filter.js")){
            foreach($this->js as &$js)
                $js = str_replace("js/", "cjs/", $js);
        }
        /* handle js end*/

        /* handle css*/
        $css_filter = array('css/common.css', 'css/jquery-ui-1.8.7.css');
        $tmp = array();
        //get relative path with WWW_ROOT
        foreach($this->css as $k=>$v){
            if($k === "plugin"){
                foreach($v as $pl=>$_v){
                    foreach($_v as $css)
                        $this->css[] = "plugins/{$pl}/css/{$css}";
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
         *
        //check for direct output
        foreach($this->css as $k=>$v){
            if(!in_array($v, $css_filter)){
                $css_out .= $p->pack(WWW_ROOT . $v, 'css');
                unset($this->css[$k]);
            }
        }
         */
        if(Configure::read("Asset.filter.css")){
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
