<?php
class NF_MobileController extends NF_Controller {

    protected $_mbase = "";
    protected $_msg = "";

    public function init(){
        c('application.encoding', 'utf-8');
        parent::init();
        $this->getRequest()->front = true;
        $this->_mbase = c("modules.mobile.base");
        $this->css[] = "m.css";
        $this->notice = c("site.name");
        if(isset($this->params['url']['m'])){
            $this->_msg = nforum_html(trim($this->params['url']['m']));
        }
    }

    public function beforeRender(){
        $this->_initAsset();

        if(NF_Session::getInstance()->isLogin){
            $u = User::getInstance();

            $login = true;
            $id = $u->userid;
            $isAdmin = $u->isAdmin();

            load("model/mail");
            $info = MailBox::getInfo($u);
            $info['new_mail'] = $info['newmail'];
            $info['full_mail'] = $info['full'];

            $info['newAt'] = $info['newReply'] = false;
            if(c('refer.enable')){
                load('model/refer');
                try{
                    if($u->getCustom('userdefine1', 2)){
                        $refer = new Refer($u, Refer::$AT);
                        $info['newAt'] = $refer->getNewNum();
                    }
                    if($u->getCustom('userdefine1', 3)){
                        $refer = new Refer($u, Refer::$REPLY);
                        $info['newReply'] = $refer->getNewNum();
                    }
                }catch(ReferNullException $e){}
            }
            $this->set($info);
        }else{
            $login = false;
            $id = "guest";
            $isAdmin = false;
        }

        $site = c("site");
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
}
