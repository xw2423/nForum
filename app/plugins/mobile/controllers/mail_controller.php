<?php
App::import("vendor", array("model/mail", "model/board", "inc/ubb"));
class MailController extends MobileAppController {

    public function beforeFilter(){
        parent::beforeFilter();
        $this->requestLogin();
    }

    public function index(){
        $this->cache(false);
        $this->notice = "收件箱";
        $type = MailBox::$IN;
        try{
            $mailBox = new MailBox(User::getInstance(), $type);
        }catch(MailBoxNullException $e){
            $this->error(ECode::$MAIL_NOBOX);
        }
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;

        App::import('vendor', "inc/pagination");
        try{
            $pagination = new Pagination($mailBox, 10);
            $mails = $pagination->getPage($p);
        }catch(MailDataNullException $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }
        $info = false;
        if($mailBox->getTotalNum() > 0){
            App::import('Sanitize');
            foreach($mails as $v){
                $info[] = array(
                    "read" => $v->isRead(),
                    "num" => $v->num,
                    "sender" => $v->OWNER,
                    "title" => Sanitize::html($v->TITLE),
                    "time" => date("Y-m-d H:i:s", $v->POSTTIME),
                    "size" => $v->EFFSIZE
                );
            }
        }
        $this->set("info", $info);
        $this->set("totalNum", $mailBox->getTotalNum());
        $this->set("curPage", $pagination->getCurPage());
        $this->set("totalPage", $pagination->getTotalPage());
    }

    public function show(){
        $this->notice = "阅读邮件";
        if(!isset($this->params['num'])){
            $this->error(ECode::$MAIL_NOMAIL);
        }
        $type = MailBox::$IN;
        $num = $this->params['num'];
        try{
            $box = new MailBox(User::getInstance(), $type);
            $mail = Mail::getInstance($num, $box);
        }catch(Exception $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }
        $mail->setRead();
        $content = $mail->getHtml();
        preg_match("|来&nbsp;&nbsp;源:[\s]*([0-9a-zA-Z.:*]+)|", $content, $f);
        $f = empty($f)?"":"<br />FROM {$f[1]}";
        $s = (($pos = strpos($content, "<br/><br/>")) === false)?0:$pos + 10;
        $e = (($pos = strpos($content, "<br/>--<br/>")) === false)?strlen($content):$pos + 7;
        $content = substr($content, $s, $e - $s) . $f;
        if(Configure::read("ubb.parse")){
            $content = XUBB::parse($content);
        }
        App::import("Sanitize");
        $this->set("num", $mail->num);
        $this->set("title", Sanitize::html($mail->TITLE));
        $this->set("sender", $mail->OWNER);
        $this->set("time", date("Y-m-d H:i:s", $mail->POSTTIME));
        $this->set("content", $content);
    }

    public function send(){
        if(!Mail::canSend())
            $this->error(ECode::$MAIL_SENDERROR);
        if($this->RequestHandler->isPost()){
            @$id = trim($this->params['form']['id']);
            @$title = trim($this->params['form']['title']);
            @$content = trim($this->params['form']['content']);
            if($this->encoding != Configure::read("App.encoding")){
                $title = iconv($this->encoding, Configure::read("App.encoding")."//IGNORE", $title);
                $content = iconv($this->encoding, Configure::read("App.encoding")."//IGNORE", $content);
            }
            $sig = 0;
            $bak = isset($this->params['form']['backup'])?1:0;
            try{
                Mail::send($id, $title, $content, $sig, $bak);
            }catch(MailSendException $e){
                $this->error($e->getMessage());
            }
            $this->redirect($this->_mbase . "/mail?m=" . ECode::$MAIL_SENDOK);
        }
        $this->notice = "新邮件";
        $uid = $title = $content = "";
        if(isset($this->params['pass'][0])){
            $type = MailBox::$IN;
            $num = $this->params['pass'][0];
            if(preg_match("|^\d+$|", $num)){
                try{
                    $mail = MAIL::getInstance($num, new MailBox(User::getInstance(),$type));
                    if(isset($this->params['url']['a']) && $this->params['url']['a'] == "r"){
                        if(!strncmp($mail->TITLE, "Re: ", 4))
                            $title = $mail->TITLE;
                        else
                            $title = "Re: " . $mail->TITLE;
                        $content = "\n".$mail->getRef();
                        //remove ref ubb tag
                        $content = XUBB::remove($content);
                        $uid = $mail->OWNER;
                    }else{
                        $title = $mail->TITLE . "(转寄)";
                        $content = preg_replace("/^发信人[^\n]*\n|^寄信人[^\n]*\n/", "", $mail->getContent());
                    }
                }catch(Exception $e){
                    $this->error(ECode::$MAIL_NOMAIL);
                }
            }else{
                try{
                    $user = User::getInstance($this->params['pass']['0']);
                }catch(UserNullException $e){
                    $this->error(ECode::$USER_NOID);
                }
                $uid = $user->userid;
            }
        }
        $this->set("uid", $uid);
        $this->set("title", $title);
        $this->set("content", $content);
    }

    public function delete(){
        $type = MailBox::$IN;
        try{
            $box = new MailBox(User::getInstance(), $type);
        }catch(MailBoxNullException $e){
            $this->error(ECode::$MAIL_NOBOX);
        }
        if(isset($this->params['pass'][0])){
            try{
                $num = (int)$this->params['pass'][0];
                $mail = Mail::getInstance($num, $box);
                if(!$mail->delete())
                    $this->error(ECode::$MAIL_DELETEERROR);
            }catch(Exception $e){
                $this->error(ECode::$MAIL_DELETEERROR);
            }
        }
        $this->redirect($this->_mbase . "/mail?m=" .  ECode::$MAIL_DELETEOK);
    }
}
?>
