<?php
load('model/mail');
class MailController extends NF_MobileController {

    public function init(){
        parent::init();
        $this->requestLogin();
    }

    public function indexAction(){
        $type = MailBox::$IN;
        if(isset($this->params['type']))
            $type = $this->params['type'];

        try{
            $mailBox = new MailBox(User::getInstance(), $type);
        }catch(MailBoxNullException $e){
            $this->error(ECode::$MAIL_NOBOX);
        }
        $this->notice = '邮件-' . $mailBox->desc;
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;

        load("inc/pagination");
        try{
            $pagination = new Pagination($mailBox, 10);
            $mails = $pagination->getPage($p);
        }catch(MailDataNullException $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }
        $info = false;
        if($mailBox->getTotalNum() > 0){
            foreach($mails as $v){
                $info[] = array(
                    "read" => $v->isRead(),
                    "num" => $v->num,
                    "sender" => $v->OWNER,
                    "title" => nforum_html($v->TITLE),
                    "time" => date("Y-m-d H:i:s", $v->POSTTIME),
                    "size" => $v->EFFSIZE
                );
            }
        }
        $this->set("type", $type);
        $this->set("info", $info);
        $this->set("totalNum", $mailBox->getTotalNum());
        $this->set("curPage", $pagination->getCurPage());
        $this->set("totalPage", $pagination->getTotalPage());
    }

    public function showAction(){
        if(!isset($this->params['type']))
            $this->error(ECode::$MAIL_NOBOX);
        if(!isset($this->params['num']))
            $this->error(ECode::$MAIL_NOMAIL);

        $type = $this->params['type'];
        $num = $this->params['num'];
        try{
            $box = new MailBox(User::getInstance(), $type);
            $mail = Mail::getInstance($num, $box);
        }catch(Exception $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }
        $this->notice = $box->desc . "-阅读邮件";
        $mail->setRead();
        $content = $mail->getHtml();
        preg_match("|来&nbsp;&nbsp;源:[\s]*([0-9a-zA-Z.:*]+)|", $content, $f);
        $f = empty($f)?"":"<br />FROM {$f[1]}";
        $s = (($pos = strpos($content, "<br/><br/>")) === false)?0:$pos + 10;
        $e = (($pos = strpos($content, "<br/>--<br/>")) === false)?strlen($content):$pos + 7;
        $content = substr($content, $s, $e - $s) . $f;
        if(c("ubb.parse")){
            load('inc/ubb');
            $content = XUBB::parse($content);
        }
        $this->set("type", $type);
        $this->set("num", $mail->num);
        $this->set("title", nforum_html($mail->TITLE));
        $this->set("sender", $mail->OWNER);
        $this->set("time", date("Y-m-d H:i:s", $mail->POSTTIME));
        $this->set("content", $content);
    }

    public function sendAction(){
        if(!Mail::canSend())
            $this->error(ECode::$MAIL_SENDERROR);
        $u = User::getInstance();
        $mail = false;
        if(isset($this->params['type']) && isset($this->params['num'])){
            $type = $this->params['type'];
            $num = $this->params['num'];
            try{
                $mail = MAIL::getInstance($num, new MailBox($u,$type));
            }catch(Exception $e){}
        }
        if($this->getRequest()->isPost()){
            $title = $content = '';
            $sig = User::getInstance()->signature;
            if(isset($this->params['form']['title']))
                $title = trim($this->params['form']['title']);
            if(isset($this->params['form']['content']))
                $content = $this->params['form']['content'];
            $sig = 0;
            $bak = isset($this->params['form']['backup'])?1:0;
            $title = nforum_iconv($this->encoding, 'GBK', $title);
            $content = nforum_iconv($this->encoding, 'GBK', $content);
            try{
                if(false === $mail){
                    //send new
                    if(!isset($this->params['form']['id']))
                        $this->error(ECode::$MAIL_NOID);
                    $id = trim($this->params['form']['id']);
                    Mail::send($id, $title, $content, $sig, $bak);
                    $this->redirect($this->_mbase . "/mail?m=" . ECode::$MAIL_SENDOK);
                }else{
                    //reply
                    $mail->reply($title, $content, $sig, $bak);
                    $this->redirect($this->_mbase . "/mail/{$type}?m=" . ECode::$MAIL_SENDOK);
                }
            }catch(MailSendException $e){
                $this->error($e->getMessage());
            }
        }

        $uid = $title = $content = "";
        if(isset($this->params['type']) && isset($this->params['num'])){
            $this->notice = "邮件-回复邮件";
            if(false === $mail){
                //reply article
                try{
                    load(array('model/board', 'model/article'));
                    $b = Board::getInstance($type);
                    if(!$b->hasReadPerm($u))
                        $this->error(ECode::$BOARD_NOPERM);
                    $mail = Article::getInstance($num, $b);
                }catch(Exception $e){
                    $this->error(ECode::$MAIL_NOMAIL);
                }
            }
            if(!strncmp($mail->TITLE, "Re: ", 4))
                $title = $mail->TITLE;
            else
                $title = "Re: " . $mail->TITLE;
            $content = "\n".$mail->getRef();
            //remove ref ubb tag
            load('inc/ubb');
            $content = XUBB::remove($content);
            $uid = $mail->OWNER;
        }else{
            $this->notice = "邮件-新邮件";
            if(isset($this->params['url']['id']))
                try{
                    $u = User::getInstance($this->params['url']['id']);
                    $uid = $u->userid;
                }catch(Exception $e){}
        }

        $this->set("uid", $uid);
        $this->set("title", $title);
        $this->set("content", $content);
        $this->set("bak", $u->getCustom("mailbox_prop", 0));
    }

    public function forwardAction(){
        if(!isset($this->params['type']))
            $this->error(ECode::$MAIL_NOBOX);
        if(!isset($this->params['num']))
            $this->error(ECode::$MAIL_NOMAIL);

        $type = $this->params['type'];
        $num = $this->params['num'];
        try{
            $box = new MailBox(User::getInstance(), $type);
            $mail = Mail::getInstance($num, $box);
        }catch(Exception $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }

        if($this->getRequest()->isPost()){
            if(!isset($this->params['form']['target']))
                $this->error(ECode::$USER_NONE);
            $target = trim($this->params['form']['target']);
            $noansi = isset($this->params['form']['noansi']);
            $big5 = isset($this->params['form']['big5']);
            try{
                $mail->forward($target, $noansi, $big5);
            }catch(MailSendException $e){
                $this->error($e->getMessage());
            }
            $this->redirect($this->_mbase . "/mail/{$type}/{$num}?m=" . ECode::$MAIL_FORWARDOK);
        }

        $this->notice = "邮件-转寄";
        load(array("model/friend"));
        $f = new Friend(User::getInstance());
        $friends = $f->getRecord(1, $f->getTotalNum());
        $ret = array();
        foreach($friends as $v){
            $ret[] = $v->userid;
        }
        $this->set('friends', $ret);
        $this->set('type', $type);
        $this->set('num',$num);
    }

    public function deleteAction(){
        if(!isset($this->params['type']))
            $this->error(ECode::$MAIL_NOBOX);
        if(!isset($this->params['num']))
            $this->error(ECode::$MAIL_NOMAIL);
        $type = $this->params['type'];
        $num = $this->params['num'];
        try{
            $box = new MailBox(User::getInstance(), $type);
        }catch(MailBoxNullException $e){
            $this->error(ECode::$MAIL_NOBOX);
        }
        try{
            $mail = Mail::getInstance($num, $box);
            if(!$mail->delete())
                $this->error(ECode::$MAIL_DELETEERROR);
        }catch(Exception $e){
            $this->error(ECode::$MAIL_DELETEERROR);
        }
        $this->redirect($this->_mbase . "/mail/{$type}?m=" .  ECode::$MAIL_DELETEOK);
    }
}
