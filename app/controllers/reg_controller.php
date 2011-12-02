<?php
/**
 * Register controller for nforum
 *
 * @author xw
 */
class RegController extends AppController{

    public function __construct(){
        parent::__construct();
        $this->components[] = 'AuthImg';
    }

    public function authImg(){
        $this->cache(false);
        $ret = $this->AuthImg->getImage();
        $this->_stop();
    }

    public function index(){
        $this->js[] = "forum.reg.js";
        $this->css[] = "register.css";
        $this->notice[] = array("url" => "", "text" => "ÓÃ»§×¢²á");
    }

    public function ajax_reg(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $IDLEN = BBS_IDLEN;
        //it's defined in bbs.h but no constant for php
        $PWDLEN = 39;
        $NAMELEN = 39;
        @$id = $this->params['form']['id'];
        @$pwd1 = $this->params['form']['passwd1'];
        @$pwd2 = $this->params['form']['passwd2'];
        @$name = $this->params['form']['name'];
        @$auth = $this->params['form']['auth'];
        @$phone = trim($this->params['form']['phone']);
        @$tname = trim($this->params['form']['tname']);
        @$gender = trim($this->params['form']['gender']);
        @$dept = trim($this->params['form']['dept']);
        @$address = trim($this->params['form']['address']);
        @$year = trim($this->params['form']['year']);
        @$month = trim($this->params['form']['month']);
        @$day = trim($this->params['form']['day']);
        @$email = trim($this->params['form']['email']);

        if(!$this->AuthImg->check($auth))
            $this->error(ECode::$REG_AUTH);
        $this->AuthImg->destory();
        if($pwd1 != $pwd2)
            $this->error(ECode::$REG_PWD);
        if(!preg_match("/^[a-zA-Z][a-zA-Z0-9]{1,".($IDLEN-1)."}$/", $id)
            || !preg_match("/^.{4,{$PWDLEN}}$/", $pwd1)
             ||!preg_match("/^.{2,{$NAMELEN}}$/", $name)
                 ||!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w)*\.\w+([-.]\w+)*/", $email)
             ||$tname == "" || $gender == "" || $dept == "" || $address == "" || $year == "" || $month == "" || $day == "")
            $this->error(ECode::$REG_FORMAT);
        if(!preg_match("/^[0-9()-]+$/", $phone))
            $this->error(ECode::$REG_AUTH);
        if($gender != '1' && $gender != '2')
            $gender = 1;
        if(!preg_match("/^(19|20)[0-9]{2}$/", $year))
            $year = "1970";
        if($month == "" || intval($month) < 1 || intval($month) > 12)
            $month = "01";
        if($day == "" || intval($day) < 1 || intval($day) >31)
            $day = "01";
        $birthday = "$year-{$month}-{$day}";

        $tname = iconv('UTF-8', 'GBK//TRANSLIT', $tname);
        $dept = iconv('UTF-8', 'GBK//TRANSLIT', $dept);
        $address = iconv('UTF-8', 'GBK//TRANSLIT', $address);

        try{
            User::create($id, $pwd1, $name);
            $u = User::getInstance($id);
            $u->reg($tname,$dept,$address,$gender,$year,$month,$day,$email,$phone,'',false);
        }catch(UserCreateException $e){
            $this->error($e->getMessage());
        }catch(UserCreateException $e){
            $this->error();
        }catch(UserRegException $e){
            $this->error($e->getMessage());
        }

        $ret['ajax_code'] = ECode::$REG_OK;
        $ret['default'] = Configure::read("site.home");
        $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
        $this->set('no_html_data', $ret);
    }

    public function form(){
        $this->requestLogin();
        $this->js[] = "forum.reg.js";
        $this->css[] = "register.css";
        $this->notice[] = array("url" => "", "text" => "ÌîÐ´×¢²áµ¥");
    }

    public function ajax_form(){
        if(!$this->RequestHandler->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);
        $this->requestLogin();
        @$auth = $this->params['form']['auth'];
        @$phone = trim($this->params['form']['phone']);
        @$tname = trim($this->params['form']['tname']);
        @$gender = trim($this->params['form']['gender']);
        @$dept = trim($this->params['form']['dept']);
        @$address = trim($this->params['form']['address']);
        @$year = trim($this->params['form']['year']);
        @$month = trim($this->params['form']['month']);
        @$day = trim($this->params['form']['day']);
        @$email = trim($this->params['form']['email']);

        if(!$this->AuthImg->check($auth))
            $this->error(ECode::$REG_AUTH);
        $this->AuthImg->destory();
        if(!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w)*\.\w+([-.]\w+)*/", $email)
             ||$tname == "" || $gender == "" || $dept == "" || $address == "" || $year == "" || $month == "" || $day == "")
            $this->error(ECode::$REG_FORMAT);
        if(!preg_match("/^[0-9()-]+$/", $phone))
            $this->error(ECode::$REG_AUTH);
        if($gender != '1' && $gender != '2')
            $gender = 1;
        if(!preg_match("/^(19|20)[0-9]{2}$/", $year))
            $year = "1970";
        if($month == "" || intval($month) < 1 || intval($month) > 12)
            $month = "01";
        if($day == "" || intval($day) < 1 || intval($day) >31)
            $day = "01";
        $birthday = "$year-{$month}-{$day}";

        $tname = iconv('UTF-8', 'GBK//TRANSLIT', $tname);
        $dept = iconv('UTF-8', 'GBK//TRANSLIT', $dept);
        $address = iconv('UTF-8', 'GBK//TRANSLIT', $address);

        try{
            $u = User::getInstance();
            $u->reg($tname,$dept,$address,$gender,$year,$month,$day,$email,$phone,'',false);
        }catch(UserNullException $e){
            $this->error(ECode::$USER_NOID);
        }catch(UserRegException $e){
            $this->error($e->getMessage());
        }

        $ret['ajax_code'] = ECode::$REG_FORMOK;
        $ret['default'] = Configure::read("site.home");
        $ret['list'][] = array("text" => Configure::read("site.name"), "url" => Configure::read("site.home"));
        $this->set('no_html_data', $ret);
    }
}
?>
