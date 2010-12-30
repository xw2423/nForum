<?php
class UserController extends MobileAppController {
    public function login(){
        if($this->RequestHandler->isPost()){
            if(!isset($this->params['form']['id']))
                $this->error(ECode::$LOGIN_NOID);
            $id = trim($this->params['form']['id']);
            $pwd="";$cookieDate = 0;
            @$pwd = trim($this->params['form']['passwd']);
            if(isset($this->params['form']['save']))
                $time = 31536000;
            else
                $time = null;
            try{
                $this->ByrSession->login($id, $pwd, false, $time);
            }catch(LoginException $e){
                $this->error($e->getMessage());
            }
            $this->redirect($this->_mbase . "/?m=" . ECode::$LOGIN_OK);
        }
        $this->redirect($this->_mbase);
    }

    public function logout(){
        $this->cache(false);
        $this->ByrSession->logout();
        $this->redirect($this->_mbase . "/?m=" . ECode::$LOGIN_OUT);
    }

    public function query(){
        $this->notice = "用户查询";
        App::import('Sanitize');
        @$id = trim($this->params['pass'][0]);
        try{
            $u = User::getInstance($id);
        }catch(UserNullException $e){
            $this->set("noid", true);
            return;
        }
        App::import("vendor", "inc/astro");
        $astro = Astro::getAstro($u->birthmonth, $u->birthday);
        $info = array(
            "uid" => $u->userid,
            "name" => Sanitize::html($u->username),
            "gender" => ($u->gender == 77)?"男":"女",
            "astro" => $astro['name'],
            "qq" => ($u->OICQ == "")?"未知":$u->OICQ,
            "msn" => ($u->MSN == "")?"未知":$u->MSN,
            "homepage" => ($u->homepage == "")?"未知":$u->homepage,
            "level" => $u->getLevel(),
            "postNum" => $u->numposts,
            "loginNum" => $u->numlogins,
            "lastTime" => date("Y-m-d H:i:s", $u->lastlogin),
            "lastIP" => $u->lasthost,
            "life" => $u->getLife(),
            "first" => date("Y-m-d", $u->firstlogin),
            "status" => $u->getStatus(),
        );
        $this->set($info);
        $this->set("hide", ($u->getCustom("userdefine0", 29) == 0));
    }
}
?>
