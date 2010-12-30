<?php
/**
 * user controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/widget"));
class UserController extends AppController {

    public function login(){
        $this->brief = true;
        $this->notice[] = array("url"=>"/login", "text"=>"登录");
        $this->css[] = "login.css";
        
        $this->cache(false);
        $from = null;
        if(isset($this->params['url']['from'])){
            $from = trim($this->params['url']['from']);
            $this->set("from", $from);
        }

        if(isset($this->params['form']['id'])
             && $this->params['form']['id'] != ""
             && isset($this->params['form']['passwd'])){
            $id = trim($this->params['form']['id']);
            $pwd = trim($this->params['form']['passwd']);
            $cookieDate = 0;
            if(isset($this->params['form']['CookieDate']))
                $cookieDate = (int) $this->params['form']['CookieDate'];
            if($id == ''){
                $this->error(ECode::$LOGIN_NOID);
            }
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

            if(is_null($from) || $from == Configure::read("site.home")){
                $this->waitDirect(
                    array(
                        "text" => Configure::read("site.name"), 
                        "url" => Configure::read("site.home")
                    ), ECode::$LOGIN_OK);
            }else{
                $this->waitDirect(
                    array(
                        "text" => $from,
                        "url" => $from 
                    ), ECode::$LOGIN_OK,
                    array(
                        array(
                            "text" => Configure::read("site.name"), 
                            "url" => Configure::read("site.home")
                        )
                    ));
            }
        }
    }

    public function logout(){
        $this->cache(false);
        $this->ByrSession->logout();
        $this->waitDirect(
            array(
                "text"=>Configure::read("site.name"), 
                "url"=>Configure::read("site.home")
            ), ECode::$LOGIN_OUT);
    }

    public function query(){
        $this->notice[] = array("url"=>"/user/query", "text"=>"查询用户");
        $this->css[] = "userinfo.css";
        $this->js[] = "forum.user.js";

        App::import('Sanitize');
        $id = trim($this->params['id']);
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
            "furl" => Sanitize::html($u->getFace()),
            "fwidth" => ($u->userface_width === 0)?"":$u->userface_width,
            "fheight" => ($u->userface_height === 0)?"":$u->userface_height,
            "gender" => ($u->gender == 77)?"男":"女",
            "astro" => $astro['name'],
            "qq" => ($u->OICQ == "")?"未知":Sanitize::html($u->OICQ),
            "msn" => ($u->MSN == "")?"未知":Sanitize::html($u->MSN),
            "homepage" => ($u->homepage == "")?"未知":Sanitize::html($u->homepage),
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

    public function info(){
        $this->requestLogin();
        $this->notice[] = array("url"=>"/user/info", "text"=>"基本资料修改");
        $this->css[] = "control.css";
        $this->js[] = "forum.user.js";

        App::import('Sanitize');
        $u = User::getInstance();
        if($this->RequestHandler->isPost()){
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
            $this->waitDirect(
                array(
                    "text" => "基本资料修改",
                    "url" => $this->path
                ), ECode::$USER_SAVEOK,
                array(
                    array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"))
                ));
        }
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

    /**
     * action for modify pwd and nickname
     */
    public function passwd(){
        $this->requestLogin();
        $this->notice[] = array("url"=>"/user/info", "text"=>"昵称密码修改");
        $this->css[] = "control.css";
        $this->js[] = "forum.user.js";

        $u = User::getInstance();
        if($this->RequestHandler->isPost()){
            if(isset($this->params['form']['name'])){
                $name = $this->params['form']['name'];
                //0 means modify forever
                if($u->setName($name)){
                    $this->waitDirect(
                        array(
                            "text" => "昵称密码修改",
                            "url" => $this->path
                        ), ECode::$USER_NAMEOK,
                        array(
                            array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"))
                        ));
                }else{
                    $this->error(ECode::$USER_NAMEERROR);
                }
            }else if(isset($this->params['form']['pold'])
                && isset($this->params['form']['pnew1'])
                && isset($this->params['form']['pnew2'])){
                    $old = trim($this->params['form']['pold']);
                    $new1 = trim($this->params['form']['pnew1']);
                    $new2 = trim($this->params['form']['pnew2']);
                    if($new1 !== $new2){
                        $this->error(ECode::$USER_PWDERROR);
                    }
                    if(!Forum::checkPwd($u->userid, $old, false, false)){
                        $this->error(ECode::$USER_OLDPWDERROR);
                    }
                    if(!$u->setPwd($new1))
                        $this->error(ECode::$USER_PWDERROR);
                    $this->waitDirect(
                        array(
                            "text" => "昵称密码修改",
                            "url" => $this->path
                        ), ECode::$USER_NAMEOK,
                        array(
                            array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"))
                        ));
            }
        }
        $this->set("name", $u->username);
    }

    public function custom(){
        $this->requestLogin();
        $this->css[] = "control.css";
        $this->js[] = "forum.control.js";
        $this->notice[] = array("url"=>"/user/custom", "text"=>"用户自定义参数");

        if($this->RequestHandler->isPost()){
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
            $this->waitDirect(
                array(
                    "text" => "用户自定义参数",
                    "url" => $this->path
                ), ECode::$USER_SAVEOK,
                array(
                    array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"))
                ));
        }
        $u = User::getInstance();
        $list = Configure::read("user.custom");
        $ret = array();
        foreach($list as $k => $item){
            foreach($item as $v)
                $ret[] = array("name" => $v[1], "desc" => $v[2], "yes" => $v[3], "no" => $v[4], "val" => $u->getCustom($k, $v[0]), "id" => "{$k}_{$v[0]}");
        }
        $this->set("custom", $ret);
    }

    /**
     * page for upload face in iframe
     * override the js array
     */
    public function uploadFace(){
        $this->requestLogin();
        $this->js[] = "forum.autofix.js";

        $this->brief = true;
        $this->set("upload", false);
        if(!$this->RequestHandler->isPost()){
            $this->set("upload", true);
            return;
        }
        $u = User::getInstance();
        $face = Configure::read("user.face");
        if (isset($this->params['form']['myface'])) {
            $errno=$this->params['form']['myface']['error'];
        } else {
            $errno = UPLOAD_ERR_PARTIAL;
        }
        switch($errno){
            case UPLOAD_ERR_OK:
                $tmpFile = $this->params['form']['myface']['tmp_name'];
                $tmpName = $this->params['form']['myface']['name'];
                if (!is_uploaded_file($tmpFile)) {
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
                if (!move_uploaded_file($tmpFile, $faceFullPath)) { 
                    $msg = "上传错误";
                    break;
                }
                $msg = "文件上传成功";
                $this->set("img", $facePath);
                $this->set("width", $imgInf[0]);
                $this->set("height",$imgInf[1]);
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
        $this->set("msg", $msg);
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
