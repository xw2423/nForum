<?php
class UserController extends NF_MobileController {
    public function loginAction(){
        if($this->getRequest()->isPost()){
            if(!isset($this->params['form']['id']))
                $this->error(ECode::$LOGIN_NOID);
            $id = trim($this->params['form']['id']);
            @$pwd = $this->params['form']['passwd'];
            $cookieDate = 0;
            if(isset($this->params['form']['save']))
                $time = 31536000;
            else
                $time = null;
            try{
                NF_Session::getInstance()->login($id, $pwd, false, $time);
            }catch(LoginException $e){
                $this->error($e->getMessage());
            }
            $this->redirect($this->_mbase . "/?m=" . ECode::$LOGIN_OK);
        }
        $this->redirect($this->_mbase);
    }

    public function logoutAction(){
        $this->cache(false);
        NF_Session::getInstance()->logout();
        $this->redirect($this->_mbase . "/?m=" . ECode::$LOGIN_OUT);
    }

    public function queryAction(){
        $this->notice = "用户查询";
        $id = trim($this->params['id']);
        try{
            $u = User::getInstance($id);
        }catch(UserNullException $e){
            $this->set("noid", true);
            return;
        }
        load("inc/astro");
        $astro = Astro::getAstro($u->birthmonth, $u->birthday);
        $info = array(
            "uid" => $u->userid,
            "name" => nforum_html($u->username),
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
        $this->set("me", $u->userid == User::getInstance()->userid);
    }
}
