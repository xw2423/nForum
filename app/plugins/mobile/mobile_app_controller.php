<?php
/**
 * if you want to use another domain on mobile version
 *
 * set $this->_mbase empty and add below to .htaccess in root
 * RewriteCond %{REQUEST_URI} ^/m(/.*)?$ [NC]
 * RewriteRule ^.*$ http://{your domain} [R,L]
 * RewriteCond %{REQUEST_URI} !^/plugins/.*$ [NC]
 * RewriteCond %{REQUEST_URI} !^/att/.*$ [NC]
 * RewriteCond %{HTTP_HOST} ^{your domain}$ [NC]
 * RewriteRule ^(.*)$ index.php?url=m/$1 [QSA]
 *
 * the resource in www directory is handler by the app not the plugin
 */
class MobileAppController extends AppController {
    
    protected $_mbase = "";
    protected $_msg = "";

    public function __construct(){
        parent::__construct();
        $this->encoding = "utf-8";
    }

    public function beforeFilter(){
        $this->_mbase = Configure::read("plugins.mobile.base");
        $this->css['plugin']['mobile'][] = "m.css";
        $this->notice = Configure::read("site.name");
        parent::beforeFilter();
        if(isset($this->params['url']['m'])){
            App::import('Sanitize');
            $this->_msg = Sanitize::html(trim($this->params['url']['m']));
        }
    }

    public function beforeRender(){
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
            $this->set("islogin", true);
            $this->set("id", User::getInstance()->userid);
            $this->set("isAdmin", User::getInstance()->isAdmin());
        }else{
            $this->set("islogin", false);
        }
        $this->_initAsset();
        $site = Configure::read("site");
        $this->set("domain", $site['domain']);
        $this->set("static", $site['static']);
        $this->set("siteName", $site['name'] . "手机版");
        $this->set("webTitle", empty($this->title)?$site['name']."手机版":$this->title);
        $this->set("encoding", $this->encoding);
        $this->set("home", $site['home']);
        $this->set("base", $this->base);
        $this->set("mbase", $this->base . $this->_mbase);
        $this->set("msg", ECode::msg($this->_msg));
        $this->set("pos", $this->notice);
        $this->set("css", $this->css);
    }

    public function error($code = null){
        if(is_null($code)){
            $code = ECode::$SYS_ERROR;
        }
        $this->_msg = $code;
        $this->notice = "发生错误";
        echo $this->render("error", "");
        $this->_stop();    
    }

    public function requestLogin(){
        if(!$this->ByrSession->isLogin){
            $this->error(ECode::$SYS_NOLOGIN);
        }
    }
}
?>
