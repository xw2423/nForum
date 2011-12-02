<?php
/**
 * Web Server Application Programming Interface
 * function : category_name
 * category : 
 *         sys - system
 *         u - user
 *         s - section
 *         b - board
 *         a - article
 *         m - mail
 *         w - widget
 */
App::import("vendor", array("inc/json"));
class WsapiController extends AppController{
    
    public function beforeFilter(){
        parent::beforeFilter();
        $this->autoRender = false;
    }

    public function afterFilter(){
        parent::afterFilter();
        $this->_stop();
    }

    /**
     * check id pwd
     * @param String $id
     * @param String $pwd
     * @param String $md5
     * @param String $ip
     * @return {v:true|false,pwd:}
     */
    public function sys_checkpwd(){
        @$id = trim($this->params['url']['id']);
        @$pwd = rawurldecode(trim($this->params['url']['pwd']));
        @$md5 = intval(trim($this->params['url']['md5']));
        @$ip = trim($this->params['url']['ip']);

        $md5 = ($md5 == 1)?true:false;
        $this->ByrSession->from = ($ip == "")?"0.0.0.0":$ip;
        if($md5){
            if(Configure::read("cookie.encryption")){
                $pwd = $this->ByrSession->decrypt($pwd);
            }
            $pwd = base64_decode($pwd);
        }
        $ret = array();
        if(Forum::checkPwd($id, $pwd, $md5, true)){
            $ret['v'] = true;
            $pwd = base64_encode(User::getInstance($id)->md5passwd);
            if(Configure::read("cookie.encryption"))
                $pwd = $this->ByrSession->encrypt($pwd);
            $ret['pwd'] = rawurlencode($pwd);
        }else{
            $ret['v'] = false;
        }
        echo BYRJSON::encode($ret);
    }

    /**
     * login from md5 password
     * @param String $id
     * @param String $pwd
     * @return null
     */
    public function sys_login(){
        if($this->ByrSession->isLogin)
            $this->_stop();
        @$id = trim($this->params['url']['id']);
        @$pwd = rawurldecode(trim($this->params['url']['pwd']));
        if(Configure::read("cookie.encryption"))
            $pwd = $this->ByrSession->decrypt($pwd);
        $pwd = base64_decode($pwd);
        //single sign-on
        $this->header('P3P: CP=CAO PSA OUR');
        try{
            $this->ByrSession->login($id, $pwd);
        }catch(LoginException $e){
            $this->_stop();
        }
    }
    
    /**
     * check whether user online
     * @param String $id
     * @return {v:true|false}
     */
    public function u_online(){
        @$id = trim($this->params['url']['id']);
        $ret['v'] = true;
        try{
            $u = User::getInstance($id);
            if($u->isOnline() === false)
                $ret['v'] = false;
        }catch(UserNullException $e){
            $ret['v'] = false;
        }
        echo BYRJSON::encode($ret);
    }

    /**
     * set id pwd
     * @param String $id
     * @param String $pwd
     */
    public function u_setpwd(){
        @$id = trim($this->params['url']['id']);
        @$pwd = trim($this->params['url']['pwd']);
        if($id == "" || $pwd == "")
            $this->error();
        $ret['v'] = true;
        try{
            $u = User::getInstance($id);
            if($u->setPwd($pwd) === false)
                $ret['v'] = false;
        }catch(UserNullException $e){
            $ret['v'] = false;
        }
        echo BYRJSON::encode($ret);
    }

    /**
     * post file via deliver
     * @param String $board
     * @param String $title
     * @param String $content
     * @return {v:}
     */
    public function a_autopost(){
        @$board = urldecode(trim($this->params['url']['board']));
        @$title = urldecode(trim($this->params['url']['title']));
        @$content = urldecode(trim($this->params['url']['content']));
        if(isset($this->params['form']['board']))
            $board = trim($this->params['form']['board']);
        if(isset($this->params['form']['title']))
            $title = trim($this->params['form']['title']);
        if(isset($this->params['form']['content']))
            $content = trim($this->params['form']['content']);
        if($board == "" || $title == "")
            $this->_stop();
        App::import("vendor", "model/article");
        $ret = Article::autoPost($board, $title, $content);
        echo BYRJSON::encode(array("v"=>$ret));
    }

    /**
     * mail file via deliver
     * @param String $board
     * @param String $title
     * @param String $content
     * @return {v:}
     */
    public function m_automail(){
        @$id = trim($this->params['url']['id']);
        @$title = urldecode(trim($this->params['url']['title']));
        @$content = urldecode(trim($this->params['url']['content']));
        if(isset($this->params['form']['id']))
            @$id   = $this->params['form']['id'];
        if(isset($this->params['form']['title']))
            @$title   = $this->params['form']['title'];
        if(isset($this->params['form']['content']))
            @$content   = $this->params['form']['content'];
        if($id == "" || $title == "")
            $this->_stop();
        App::import("vendor", "model/mail");
        $ret = Mail::autoMail(User::getInstance($id), $title, $content);
        echo BYRJSON::encode(array("v"=>$ret));
    }

    public function sys_test(){echo "hello world!";}
}
?>
