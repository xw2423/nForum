<?php
/**
 * the resource in www directory is handler by the app not the plugin
 */
class MobileAppController extends AppController {
    
    protected $_mbase = "";
    protected $_msg = "";

    public function __construct(){
        parent::__construct();
        //delete RedirectAcl components
        array_pop($this->components);
        $this->encoding = "utf-8";
        if(true === Configure::read("plugins.mobile.use_domain")){
            Configure::write("plugins.mobile.base", "");
            Configure::write('site.prefix', '');
        }
        $this->front = true;
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

    //no app_controller afterFilter
    public function afterFilter(){}

    public function beforeRender(){
        if($this->ByrSession->isLogin){
            App::import("vendor", "model/mail");
            try{
                $box = new MailBox(User::getInstance(), MailBox::$IN);
                $info = $box->getInfo();
            }catch(MailBoxNullException $e){
            }catch(UserNullException $e){
            }
            $login = true;
            $id = User::getInstance()->userid;
            $isAdmin = User::getInstance()->isAdmin();
            $this->set("mailInfo", $info);
        }else{
            $login = false;
            $id = "guest";
            $isAdmin = false;
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
        $this->set("islogin", $login);
        $this->set("id", $id);
        $this->set("isAdmin", $isAdmin);
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
