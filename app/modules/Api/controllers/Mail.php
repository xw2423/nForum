<?php
load("model/mail");
class MailController extends NF_ApiController {

    public function init(){
        parent::init();
        $this->requestLogin();
    }

    public function indexAction(){
        if(!isset($this->params['type'])){
            $this->error(ECode::$MAIL_NOBOX);
        }
        if(!isset($this->params['num'])){
            $this->error(ECode::$MAIL_NOMAIL);
        }
        $type = $this->params['type'];
        $num = (int)$this->params['num'];
        try{
            $box = new MailBox(User::getInstance(), $type);
            $mail = Mail::getInstance($num, $box);
        }catch(Exception $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }
        $mail->setRead();
        $wrapper = Wrapper::getInstance();
        $this->set('data', $wrapper->mail($mail, array('content' => true)));
    }

    public function boxAction(){
        if(!isset($this->params['type'])){
            $this->error(ECode::$MAIL_NOBOX);
        }

        $type = $this->params['type'];
        try{
            $mailBox = new MailBox(User::getInstance(), $type);
        }catch(MailBoxNullException $e){
            $this->error(ECode::$MAIL_NOBOX);
        }

        $count = isset($this->params['url']['count'])?$this->params['url']['count']:c("pagination.mail");
        if(($count = intval($count)) <= 0)
            $count = c('pagination.mail');
        if($count > c('modules.api.page_item_limit'))
            $count = c('pagination.mail');
        $page = isset($this->params['url']['page'])?$this->params['url']['page']:1;
        $page = intval($page);
        $pagination = new Pagination($mailBox, $count);
        $mails = $pagination->getPage($page);

        $wrapper = Wrapper::getInstance();
        $info = array();
        foreach($mails as $v){
            $info[] = $wrapper->mail($v);
        }
        $data['description'] = $mailBox->desc;
        $data['pagination'] = $wrapper->page($pagination);
        $data['mail'] = $info;
        $this->set('data', $data);
        $this->set('root', 'mailbox');
    }

    public function infoAction(){
        $wrapper = Wrapper::getInstance();
        $this->set('data', $wrapper->mailbox());
        $this->set('root', 'mailbox');
    }

    public function sendAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!Mail::canSend())
            $this->error(ECode::$MAIL_SENDERROR);

        @$id = trim($this->params['form']['id']);
        @$title = strval(trim($this->params['form']['title']));
        @$content = strval(trim($this->params['form']['content']));

        $title = rawurldecode($title);
        $content = rawurldecode($content);
        $title = nforum_iconv($this->encoding, 'GBK', $title);
        $content = nforum_iconv($this->encoding, 'GBK', $content);

        $sig = User::getInstance()->signature;
        $bak = 0;
        if(isset($this->params['form']['signature']))
            $sig = intval($this->params['form']['signature']);
        if(isset($this->params['form']['backup']) && $this->params['form']['backup'] == 1)
            $bak = 1;

        try{
            Mail::send($id, $title, $content, $sig, $bak);
        }catch(MailSendException $e){
            $this->error($e->getMessage());
        }
        $data = array('status'=>true);
        $this->set('data', $data);
    }

    public function replyAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['type']))
            $this->error(ECode::$MAIL_NOBOX);
        if(!isset($this->params['num']))
            $this->error(ECode::$MAIL_NOMAIL);
        $type = $this->params['type'];
        $num = $this->params['num'];

        @$title = strval(trim($this->params['form']['title']));
        @$content = strval(trim($this->params['form']['content']));

        $title = rawurldecode($title);
        $content = rawurldecode($content);
        $title = nforum_iconv($this->encoding, 'GBK', $title);
        $content = nforum_iconv($this->encoding, 'GBK', $content);

        $sig = User::getInstance()->signature;
        $bak = User::getInstance()->getCustom("mailbox_prop", 0);
        if(isset($this->params['form']['signature']))
            $sig = intval($this->params['form']['signature']);
        if(isset($this->params['form']['backup']) && $this->params['form']['backup'] == 1)
            $bak = 1;

        try{
            $box = new MailBox(User::getInstance(), $type);
            $mail = Mail::getInstance($num, $box);
            $mail->reply($title, $content, $sig, $bak);
            $wrapper = Wrapper::getInstance();
            $data = $wrapper->mail($mail);
        }catch(MailBoxNullException $e){
            $this->error(ECode::$MAIL_NOBOX);
        }catch(MailNullException $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }catch(MailSendException $e){
            $this->error($e->getMessage());
        }

        $this->set('data', $data);
    }

    public function forwardAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['form']['target']))
            $this->error(ECode::$USER_NONE);
        if(!isset($this->params['type']))
            $this->error(ECode::$MAIL_NOBOX);
        if(!isset($this->params['num']))
            $this->error(ECode::$MAIL_NOMAIL);
        $type = $this->params['type'];
        $num = $this->params['num'];
        $target = trim($this->params['form']['target']);
        $noansi = (isset($this->params['form']['noansi']) && $this->params['form']['noansi'] == 1);
        $big5 = (isset($this->params['form']['big5']) && $this->params['form']['big5'] == 1);

        try{
            $box = new MailBox(User::getInstance(), $type);
            $mail = Mail::getInstance($num, $box);
            $mail->forward($target, $noansi, $big5);
            $wrapper = Wrapper::getInstance();
            $data = $wrapper->mail($mail);
        }catch(MailBoxNullException $e){
            $this->error(ECode::$MAIL_NOBOX);
        }catch(MailNullException $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }catch(MailSendException $e){
            $this->error($e->getMessage());
        }

        $this->set('data', $data);
    }

    public function deleteAction(){
        if(!$this->getRequest()->isPost())
            $this->error(ECode::$SYS_REQUESTERROR);

        if(!isset($this->params['type']))
            $this->error(ECode::$MAIL_NOBOX);
        if(!isset($this->params['num']))
            $this->error(ECode::$MAIL_NOMAIL);

        $type = $this->params['type'];
        $num = $this->params['num'];
        try{
            $box = new MailBox(User::getInstance(), $type);
            $mail = Mail::getInstance($num, $box);
            $wrapper = Wrapper::getInstance();
            $data = $wrapper->mail($mail);
            if(!$mail->delete())
                $this->error(ECode::$MAIL_DELETEERROR);
        }catch(MailBoxNullException $e){
            $this->error(ECode::$MAIL_NOBOX);
        }catch(MailNullException $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }catch(Exception $e){
            $this->error(ECode::$MAIL_DELETEERROR);
        }

        $this->set('data', $data);
    }
}
