<?php
/**
 * user controller for nforum
 *
 * @author xw
 */
App::import('vendor', 'model/widget');
class UserController extends AppController {

    public function beforeFilter(){
        //flash mode will post cookie data, so parse to system cookie first
        if ($this->RequestHandler->isFlash()) {
            if (isset($this->params['form']['cookie'])) {
                $cookie = $this->params['form']['cookie'];
                $prefix = Configure::read('cookie.prefix');
                $cookie = explode('; ', $cookie);
                foreach ($cookie as $c) {
                    list($name, $content) = split('=', $c);
                    if (preg_match("/^$prefix\[(.*)\]$/", $name, $matches)) {
                        $_COOKIE[$prefix][$matches[1]] = $content;
                    } else {
                        $_COOKIE[$name] = $content;
                    }
                }
            }
            if (isset($this->params['form']['emulate_ajax'])) {
                putenv('HTTP_X_REQUESTED_WITH=XMLHttpRequest');
            }
        }
        parent::beforeFilter();
    }

    public function ajax_session(){
        $this->cache(false);
        $static = Configure::read('site.static');
        $base = Configure::read('site.prefix');
        $user = User::getInstance();
        
        App::import('vendor', 'inc/wrapper');
        $wrapper = Wrapper::getInstance();
        $ret = $wrapper->user($user);
        $ret['is_login'] = ($user->userid != 'guest');
        $ret['forum_totol_count'] = Forum::getOnlineNum();
        $ret['forum_user_count'] = Forum::getOnlineUserNum();
        $ret['forum_guest_count'] = Forum::getOnlineGuestNum();

        $this->set('no_html_data', $ret);
    }

    public function ajax_login(){
        $this->cache(false);
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['form']['id']))
            $this->error(ECode::$LOGIN_NOID);
        if(!isset($this->params['form']['passwd']))
            $this->error(ECode::$LOGIN_ERROR);
        $id = trim($this->params['form']['id']);
        $pwd = $this->params['form']['passwd'];
        if($id == '')
            $this->error(ECode::$LOGIN_NOID);

        $cookieDate = 0;
        if(isset($this->params['form']['CookieDate']))
            $cookieDate = (int) $this->params['form']['CookieDate'];
        switch ($cookieDate) {
            case 1;
                $time = 86400; //24*60*60 sec
                break;
            case 2;
                $time = 2592000; //30*24*60*60 sec
                break;
            case 3:
                $time = 31536000; //365*24*60*60 sec
                break;
            default:
                $time = null;
        }
        try{
            $this->ByrSession->login($id, $pwd, false, $time);
        }catch(LoginException $e){
            $this->error($e->getMessage());
        }
        $this->ajax_session();
    }

    public function ajax_logout(){
        $this->cache(false);
        $this->ByrSession->logout();
    }

    /**
     * check id status
     * 0 -- unused
     * 1 -- baned
     * 2 -- small
     * 3 -- baned
     * 4 -- exist
     * 5 -- long
     * @param String $id
     */
    public function ajax_valid_id(){
        if(!isset($this->params['url']['id']))
            $this->error();
        $ret = bbs_is_invalid_id(trim($this->params['url']['id']));
        $this->set('no_html_data', array('status'=>$ret));
    }

    public function ajax_query(){
        App::import('Sanitize');
        $id = trim($this->params['id']);
        try{
            $u = User::getInstance($id);
        }catch(UserNullException $e){
            $this->error(ECode::$USER_NOID);
        }
        App::import("vendor", "inc/wrapper");
        $wrapper = Wrapper::getInstance();
        $ret = $wrapper->user($u);
        $ret['status'] = $u->getStatus();
        $this->set('no_html_data', $ret);
    }

    public function info(){
        $this->requestLogin();
        $this->notice[] = array("url"=>"/user/info", "text"=>"基本资料修改");
        $this->css[] = "control.css";
        $this->js[] = "forum.control.js";

        App::import('Sanitize');
        $u = User::getInstance();
        $ret = array(
            "gender" => ($u->gender == 77)?1:2,
            "year" => intval($u->birthyear) + 1900,
            "month" => $u->birthmonth,
            "day" => $u->birthday,
            "myface" => Sanitize::html($u->getFace()),
            "myface_url" => Sanitize::html($u->userface_url),
            "myface_w" => $u->userface_width,
            "myface_h" => $u->userface_height,
            "qq" => Sanitize::html($u->OICQ),
            "msn" => Sanitize::html($u->MSN),
            "homepage" => Sanitize::html($u->homepage),
            "email" => Sanitize::html($u->reg_email),
            "sig" => Sanitize::html($u->getSignature())
        );
        $this->set($ret);

    }

    public function ajax_info(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        extract($this->params['form']);
        if(empty($furl) || strpos($furl, Configure::read("user.face.dir"). "/") === 0){
            try{
                $u->setInfo($gender, $year, $month,$day,$email, $qq, $msn,  $homepage, 0, $furl, intval($fwidth), intval($fheight));
            }catch(UserSaveException $e){
                $this->error($e->getMessage());
            }
            $this->_clearFace(basename($furl));
        }
        $u->setSignature($signature);
        $ret['ajax_code'] = ECode::$USER_SAVEOK;
        $this->set('no_html_data', $ret);
    }

    /**
     * action for modify pwd and nickname
     */
    public function passwd(){
        $this->requestLogin();
        $this->notice[] = array("url"=>"/user/info", "text"=>"昵称密码修改");
        $this->css[] = "control.css";
        $this->js[] = "forum.control.js";

        $u = User::getInstance();
        $this->set("name", $u->username);
    }

    public function ajax_passwd(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        if(isset($this->params['form']['name'])){
            $name = $this->params['form']['name'];
            $name = iconv('UTF-8', 'GBK//TRANSLIT', $name);
            //0 means modify forever
            if($u->setName($name)){
                $ret['ajax_code'] = ECode::$USER_NAMEOK;
                $this->set('no_html_data', $ret);
            }else{
                $this->error(ECode::$USER_NAMEERROR);
            }
        }else if(isset($this->params['form']['pold'])
            && isset($this->params['form']['pnew1'])
            && isset($this->params['form']['pnew2'])){
                $old = $this->params['form']['pold'];
                $new1 = $this->params['form']['pnew1'];
                $new2 = $this->params['form']['pnew2'];
                if($new1 !== $new2){
                    $this->error(ECode::$USER_PWDERROR);
                }
                if(!Forum::checkPwd($u->userid, $old, false, false)){
                    $this->error(ECode::$USER_OLDPWDERROR);
                }
                if(!$u->setPwd($new1))
                    $this->error(ECode::$USER_PWDERROR);
                $ret['ajax_code'] = ECode::$USER_PWDOK;
                $this->set('no_html_data', $ret);
        }
    }

    public function custom(){
        $this->requestLogin();
        $this->css[] = "control.css";
        $this->js[] = "forum.control.js";
        $this->notice[] = array("url"=>"/user/custom", "text"=>"用户自定义参数");

        $u = User::getInstance();
        $list = Configure::read("user.custom");
        $ret = array();
        foreach($list as $k => $item){
            foreach($item as $v)
                $ret[] = array("name" => $v[1], "desc" => $v[2], "yes" => $v[3], "no" => $v[4], "val" => $u->getCustom($k, $v[0]), "id" => "{$k}_{$v[0]}");
        }
        $this->set("custom", $ret);
    }

    public function ajax_custom(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        $list = Configure::read("user.custom");
        $val = array();
        foreach($list as $k => $item){
            $arr = array();
            foreach($item as $v){
                if(isset($this->params['form']["{$k}_{$v[0]}"])){
                    $arr[] = array("pos"=>$v[0], "val"=>intval($this->params['form']["{$k}_{$v[0]}"]));
                    if($k == "userdefine1" && $v[0] == 31 && intval($this->params['form']["{$k}_{$v[0]}"]) === 0){
                        App::import("vendor", array("mode/widget"));
                        Widget::w3to2($u);    
                    }
                }
            }
            $val[$k] = $arr;
        }
        $u->setCustom($val);
        $ret['ajax_code'] = ECode::$USER_SAVEOK;
        $this->set('no_html_data', $ret);
    }

    /**
     * page for upload face in iframe
     * override the js array
     */
    public function ajax_face(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        $u = User::getInstance();
        $face = Configure::read("user.face");

        //init upload file
        if(isset($this->params['url']['name'])){
            //html5 mode
            $tmp_name = tempnam(CACHE, "upload_");
            file_put_contents($tmp_name, file_get_contents('php://input'));
            $file = array(
                'tmp_name' => $tmp_name,
                'name' => @iconv('utf-8', $this->encoding . "//TRANSLIT",$this->params['url']['name']),
                'size' => filesize($tmp_name),
                'error' => 0
            );
        }else if(isset($this->params['form']['file'])
            && is_array($this->params['form']['file'])){
            //flash mode
            $file = $this->params['form']['file'];
            $file['name'] = @iconv('utf-8', $this->encoding . "//TRANSLIT",$file['name']);
        }else{
            $this->error(ECode::$ATT_NONE);
        }

        $errno = isset($file['error'])?$file['error']:UPLOAD_ERR_NO_FILE;
        switch($errno){
            case UPLOAD_ERR_OK:
                $tmpFile = $file['tmp_name'];
                $tmpName = $file['name'];
                if (!isset($tmp_name) && !is_uploaded_file($tmpFile)) {
                    $msg = "上传错误";
                    break;
                }
                $ext = strrchr($tmpName, '.'); 
                if(!in_array(strtolower($ext), $face['ext'])){
                    $msg = "上传文件扩展名有误";
                    break;
                }
                if(filesize($tmpFile) > $face['size']){
                    $msg = "文件大小超过上限" . $face['size']/1024 . "K";
                    break;
                }
                mt_srand();
                $faceDir = $face['dir'] . DS . strtoupper(substr($u->userid,0,1));
                $facePath = $faceDir . DS . $u->userid . "." . mt_rand(0, 10000) . $ext;
                $faceFullDir = WWW_ROOT . $faceDir;
                $faceFullPath = WWW_ROOT . $facePath;
                if(!is_dir($faceFullDir)){
                    @mkdir($faceFullDir);
                }
                if(is_file($faceFullPath)){
                    $msg = "我觉得您今天可以买彩票了";
                    break;
                }
                $imgInf = @getimagesize($tmpFile);
                if($imgInf === false){
                    $msg = "上传的文件貌似不是图像文件";
                    break;
                }
                if(!in_array($imgInf[2], range(1, 3))){
                    $msg = "上传的文件貌似不是图像文件";
                    break;
                }
                if (isset($tmp_name)) { 
                    if(!rename($tmp_name, $faceFullPath)){
                        $msg = "上传错误";
                        break;
                    }
                }else if (!move_uploaded_file($tmpFile, $faceFullPath)) { 
                    $msg = "上传错误";
                    break;
                }
                $msg = "文件上传成功";

                $this->set("no_html_data", array(
                    "img" => $facePath
                    ,"width" => $imgInf[0]
                    ,"height" => $imgInf[1]
                    ,"ajax_st" => 1
                    ,"ajax_code" =>ECode::$SYS_AJAXOK
                    ,"ajax_msg" => $msg
                ));
                return;
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $msg = "文件大小超过系统上限";
                break;
            case UPLOAD_ERR_PARTIAL:
                $msg = "文件传输出错！";
                break;
            case UPLOAD_ERR_NO_FILE:
                $msg = "没有文件上传！";
                break;
            default:
                $msg = "未知错误";
        }
        $this->set("no_html_data", array(
            "ajax_st" => 0
            ,"ajax_code" =>ECode::$SYS_AJAXERROR
            ,"ajax_msg" => $msg
        ));
    }

    private function _clearFace($exclude){
        $u = User::getInstance();
        $faceDir = Configure::read("user.face.dir"). DS . strtoupper(substr($u->userid,0,1));
        $faceFullDir = WWW_ROOT . $faceDir;
        if ($hDir = @opendir($faceFullDir)) {
            while($file = readdir($hDir)){                                                                 
                if(preg_match("/{$u->userid}\./", $file) && $file !== $exclude)
                    unlink($faceFullDir . DS . $file); 
            }
            closedir($hDir);
        }                     
    }
}
?>
