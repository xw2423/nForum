<?php
/**
 * Mail controller for nforum
 *
 * @author xw
 */
App::import("vendor", array("model/mail", "model/article", "model/board", "model/friend", "inc/ubb"));
class MailController extends AppController {
    
    public function beforeFilter(){
        parent::beforeFilter();
        $this->requestLogin();
        $this->notice[] = array("url"=>"/mail", "text"=>"用户信件服务");
    }

    public function index(){
        $this->js[] = "forum.mail.js";
        $this->css[] = "mail.css";

        $this->cache(false);
        $type = MailBox::$IN;
        $pageBar = "";
        if(isset($this->params['type'])){
            $type = $this->params['type'];
        }
        try{
            $mailBox = new MailBox(User::getInstance(), $type);
        }catch(MailBoxNullException $e){
            $this->error(ECode::$MAIL_NOBOX);
        }
        $p = isset($this->params['url']['p'])?$this->params['url']['p']:1;

        App::import('vendor', "inc/pagination");
        try{
            $pagination = new Pagination($mailBox, Configure::read("pagination.mail"));
            $mails = $pagination->getPage($p);
        }catch(MailDataNullException $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }
        if($mailBox->getTotalNum() > 0){
            $info = array();
            App::import('Sanitize');
            foreach($mails as $v){
                $info[] = array(
                    "tag" => $this->_getTag($v),
                    "read" => $v->isRead(),
                    "num" => $v->num,
                    "sender" => $v->OWNER,
                    "title" => Sanitize::html($v->TITLE),
                    "time" => date("Y-m-d H:i:s", $v->POSTTIME),
                    "size" => $v->EFFSIZE
                );
            }
            $this->set("info", $info);
        }
        $link = "?p=%page%";
        $pageBar = $pagination->getPageBar($p, $link);
        $this->set("type", $type);
        $this->set("desc", $mailBox->desc);
        $this->set("pageBar", $pageBar);
        $this->set("totalNum", $mailBox->getTotalNum());
        $this->set("curPage", $pagination->getCurPage());
        $this->set("totalPage", $pagination->getTotalPage());
    }

    public function detail(){
        $this->css[] = "mail.css";
        $this->css[] = "ansi.css";
        $this->js[] = "forum.mail.js";

        if(!isset($this->params['type'])){
            $this->error(ECode::$MAIL_NOBOX);
        }
        if(!isset($this->params['num'])){
            $this->error(ECode::$MAIL_NOMAIL);
        }
        $type = $this->params['type'];
        $num = $this->params['num'];
        try{
            $box = new MailBox(User::getInstance(), $type);
            $mail = Mail::getInstance($num, $box);
        }catch(Exception $e){
            $this->error(ECode::$MAIL_NOMAIL);
        }
        $mail->setRead();
        $content = $mail->getHtml(true);
        if(Configure::read("ubb.parse")){
            $content = preg_replace("'^(.*?<br \/>.*?<br \/>)'e", "XUBB::remove('\\1')", $content);
            $content = XUBB::parse($content);
        }
        App::import("Sanitize");
        $this->set("num", $mail->num);
        $this->set("type", $box->type);
        $this->set("content", $content);
        $this->notice[] = array("url"=>"/mail/$type", "text"=>$box->desc);
    }

    public function send(){
        $this->_sendInit();
        if($this->RequestHandler->isPost()){
            @$id = trim($this->params['form']['id']);
            @$title = trim($this->params['form']['title']);
            @$content = trim($this->params['form']['content']);
            @$sig = intval($this->params['form']['signature']);
            $bak = isset($this->params['form']['backup'])?1:0;
            try{
                Mail::send($id, $title, $content, $sig, $bak);
            }catch(MailSendException $e){
                $this->error($e->getMessage());
            }
            $this->waitDirect(
                array(
                    "text" => "收件箱", 
                    "url" => "/mail/"
                ), ECode::$MAIL_SENDOK);
        }
        $this->js[] = "forum.mail.js";
        $this->css[] = "post.css";
        $this->notice[] = array("url"=>"/mail/send", "text"=>"撰写邮件");

        $title = $content = "";

        //no attachment when forward
        //I will not use kbs function which is not well to handle forwarding,article and mail should have union function to forward
        if(isset($this->params['url']['id'])){
            try{
                $user = User::getInstance($this->params['url']['id']);
            }catch(UserNullException $e){
                $this->error(ECode::$USER_NOID);
            }
            $this->set("rid", $user->userid);
        }else{
            //show my friends
            $f = new Friend(User::getInstance());
            $friends = $f->getRecord(1, $f->getTotalNum());
            $ret = array();
            foreach($friends as $v){
                $ret[$v->userid] = $v->userid;
            }
            $this->set("friends", $ret);
        }
        if(preg_match("/^\/mail\/reply/", $this->path)){
            if(!isset($this->params['type'])){
                $this->error(ECode::$MAIL_NOBOX);
            }
            if(!isset($this->params['num'])){
                $this->error(ECode::$MAIL_NOMAIL);
            }
            $type = $this->params['type'];
            $num = $this->params['num'];
            try{
                $mail = MAIL::getInstance($num, new MailBox(User::getInstance(),$type));
            }catch(UserNullException $e){
                $this->error(ECode::$MAIL_NOMAIL);
            }catch(Exception $e){
                try{
                    $mail = Article::getInstance($num, Board::getInstance($type));
                }catch(Exception $e){
                    $this->error(ECode::$ARTICLE_NOREPLY);
                }
            }
            if(!strncmp($mail->TITLE, "Re: ", 4))
                $title = $mail->TITLE;
            else
                $title = "Re: " . $mail->TITLE;
            $content = "\n".$mail->getRef();
            //remove ref ubb tag
            $content = XUBB::remove($content);
            $this->set("rid", $mail->OWNER);
        }else if(preg_match("/^\/mail\/forward/", $this->path)){
            if(!isset($this->params['type'])){
                $this->error(ECode::$MAIL_NOBOX);
            }
            if(!isset($this->params['num'])){
                $this->error(ECode::$MAIL_NOMAIL);
            }
            $type = $this->params['type'];
            $num = $this->params['num'];
            try{
                $mail = MAIL::getInstance($num, new MailBox(User::getInstance(),$type));
            }catch(MailBoxNullException $e){
                $this->error(ECode::$MAIL_NOBOX);
            }catch(MailNullException $e){
                $this->error(ECode::$MAIL_NOMAIL);
            }
            $title = $mail->TITLE . "(转寄)";
            $content = preg_replace("/^发信人[^\n]*\n|^寄信人[^\n]*\n/", "", $mail->getContent());
        }else if(preg_match("/^\/article\/forward/", $this->path)){
            $brd = $this->params['name'];
            $id = $this->params['id'];
            try{
                $article = Article::getInstance($id, Board::getInstance($brd));
            }catch(BoardNullException $e){
                $this->error(ECode::$ARTICLE_NONE);
            }catch(ArticleNullException $e){
                $this->error(ECode::$BOARD_NONE);
            }
            $title = $article->TITLE . "(转寄)";
            $content = preg_replace("/^发信人[^\n]*\n|^寄信人[^\n]*\n/", "", $article->getContent());
        }
        $u = User::getInstance();
        $sigOption = array();
        foreach(range(0, $u->signum) as $v){
            if($v == 0)
                $sigOption["$v"] = "不使用签名档";
            else
                $sigOption["$v"] = "使用第{$v}个";
        }
        $sigOption["-1"] = "使用随机签名档";
        $this->set("title", $title);
        $this->set("content", $content);
        $this->set("sigOption", $sigOption);
        $this->set("sigNow", $u->signature);
    }

    public function delete(){
        if(!isset($this->params['type'])){
            $this->error(ECode::$MAIL_NOBOX);
        }
        $type = $this->params['type'];
        try{
            $box = new MailBox(User::getInstance(), $type);
        }catch(MailBoxNullException $e){
            $this->error(ECode::$MAIL_NOBOX);
        }
        if(!isset($this->params['num'])){
            if(!$this->RequestHandler->isPost()){
                $this->error(ECode::$MAIL_DELETEERROR);
            }
            if(isset($this->params['form']['all'])){
                //delete all
                try{
                    $all = $box->getRecord(1, $box->getTotalNum());
                    foreach($all as $mail)
                        $mail->delete(); 
                }catch(Exception $e){
                    continue;
                }
            }else{
                //delete normal
                foreach($this->params['form'] as $k=>$v){
                    if(!preg_match("/m_/", $k))
                        continue;
                    $num = split("_", $k);
                    try{
                        $mail = Mail::getInstance($num[1], $box);
                        $mail->delete();
                    }catch(Exception $e){
                        continue;
                    }
                }
            }
        }else{
            try{
                $num = $this->params['num'];
                $mail = Mail::getInstance($num, $box);
                if(!$mail->delete())
                    $this->error(ECode::$MAIL_DELETEERROR);
            }catch(Exception $e){
                $this->error(ECode::$MAIL_DELETEERROR);
            }
        }
        $this->waitDirect(
            array(
                "text" => $box->desc, 
                "url" => "/mail/$type"
            ), ECode::$MAIL_DELETEOK);
    }

    private function _sendInit(){
        if(!Mail::canSend())
            $this->error(ECode::$MAIL_SENDERROR);
    }

    private function _getTag($mail){
        $ret = "";
        $ret .= $mail->isRead()?"n" : "N" ;
        $ret .= $mail->isM()?"m" : " " ;
        $ret .= $mail->isReply()?"r" : " "; 
        $ret .= $mail->hasAttach()?"@" : " "; 
        return $ret;
    }
}
?>
