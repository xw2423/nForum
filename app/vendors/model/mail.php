<?php
/****************************************************
 * FileName: app/vendors/model/mail.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
App::import("vendor", array("model/archive", "inc/pagination", "inc/code"));

/**
 * class MailBox is the collection of mail
 *
 * @implements Pageable
 * @author xw
 */
class MailBox implements Pageable{
    public static $IN = "inbox";
    public static $OUT = "outbox";
    public static $REC = "deleted";

    /**
     * the static variable of MailBox
     * @var string $type
     */
    public $type;

    /**
     * chinese desc of mail box
     * @var string $desc
     */
    public $desc;

    /**
     * reference of User
     * @var User $user
     */
    public $user;

    /** the path of MailBox */
    public $path;

    public static function getInfo($user){
        return bbs_mail_get_num($user->userid);
    }

    public static function getSpace(){
        return bbs_getmailusedspace();
    }

    /**
     * function __construct
     *
     * @param User $user
     * @param string $type static variable of MailBox
     * @return MailBox object
     * @access public
     * @throws MailBoxNullException
     * @example
     * try{
     *         $box = new MailBox(User::getInstance(), MailBox::$IN);
     * }catch(MailBoxNullException){}
     */
    public function __construct($user, $type) {
        if(!is_a($user, "User"))
            throw new MailBoxNullException();
        $this->user = $user;
        $this->type = $type;
        switch($type){
            case self::$IN:
                $this->desc = "收件箱";
                $this->path = ".DIR";
                break;
            case self::$OUT:
                $this->desc = "发件箱";
                $this->path = ".SENT";
                break;
            case self::$REC:
                $this->desc = "垃圾箱";
                $this->path = ".DELETED";
                break;
            default :
                throw new MailBoxNullException();
        }
    }

    public function getTotalNum(){
        return $this->getMailNum();
    }

    public function getRecord($start, $num){
        $start =  $this->getMailNum() - $start + 1 - $num;
        $arr = bbs_getmails($this->getFullPath(), $start, $num);
        if(!$arr)
            throw new MailDataNullException();
        else if($arr == -1){
            return array();
        }
        foreach($arr as $k=>&$v){
            $v = new Mail($v, $this, $start + $k);
        }
        return array_reverse($arr);
    }

    public function getMailNum(){
         return bbs_getmailnum2($this->getFullPath());
    }

    public function getNewNum(){
        $mails = $this->getRecord(1, $this->getTotalNum());
        $num = 0;
        foreach($mails as $v){
            if(!$v->isRead())
                $num ++;
        }
        return $num;
    }

    /**
     * function getFullPath get the full path of mail box base on BBS_HOME
     *
     * @return string
     * @access public
     */
    public function getFullPath(){
        return bbs_setmailfile($this->user->userid, $this->path);
    }

}

/**
 * class Mail is the single mail in kbs
 *
 * @extends Archive
 * @author xw
 */
class Mail extends Archive {

    /** the position is mail box */
    public $num;

    /** reference of mail box */
    private $_box;

    /**
     * function getInstance get a Mail object via $box & $num
     * suggest using this method to get a ref of mail
     *
     * @param int $num
     * @param MailBox $box
     * @return Mail object
     * @static
     * @access public
     * @throws MailNullException
     */
    public static function getInstance($num, $box){
        $arr = array();
        if(bbs_get_records_from_num($box->getFullPath(), $num, $arr))
            return new Mail($arr[0], $box, $num);
        else
            throw new MailNullException();
    }

    public static function autoMail($user, $title, $content = ""){
        $file = tempnam(TMP . 'cache', "automail");
        $fp = fopen($file,"w");
        $content = str_replace('\n', "\n", $content);
        fwrite($fp,"$content\n");
        fclose($fp);
        $ret = bbs_mail_file('deliver', $file, $user->userid, $title, 0);
        unlink($file);
        return $ret;
    }

    /**
     * function canSend check current user has send right
     *
     * @return boolean true|false
     * @static
     * @access public
     */
    public static function canSend(){
        return (bbs_can_send_mail() > 0);
    }

    public static function send($id, $title, $content, $sig, $bak){
        $code = null;
        $ret = bbs_postmail($id, $title, $content, $sig, $bak);
        switch($ret){
            case -1:
                $code = ECode::$SYS_NOTMPFILE;
                break;
            case -2:
            case -6:
            case -7:
                $code = ECode::$MAIL_ERROR;
                break;
            case -3:
                $code = ECode::$MAIL_REJECT;
                break;
            case -4:
                $code = ECode::$MAIL_FULL;
                break;
            case -5:
                $code = ECode::$POST_FREQUENT;
                break;
            case -8:
                $code = ECode::$MAIL_RENUMERROR;
                break;
            case -9:
                $code = ECode::$MAIL_NOPERM;
                break;
            case -100:
                $code = ECode::$MAIL_NOID;
                break;
        }
        if(!is_null($code))
            throw new MailSendException($code);
    }

    /**
     * function __contstruct()
     * do not use this to get a object
     *
     * @param array $info
     * @param MailBox $box
     * @param int $num
     * @return Mail
     * @access public
     * @throws MailNullException
     */
    public function __construct($info, $box, $num){
        try{
            parent::__construct($info);
        }catch(ArchiveNullException $e){
            throw new MailNullException();
        }
        if(!is_a($box, "MailBox"))
            throw new MailNullException();
        $this->num = $num;
        $this->_box = $box;
    }

    public function update($title, $content){return false;}

    public function reply($title, $content, $sig, $bak){
        $code = null;
        $ret = bbs_postmail($this->_box->getFullPath(), $this->FILENAME, $this->num, $title, $content, $sig, $bak);
        switch($ret){
            case -1:
                $code = ECode::$SYS_NOTMPFILE;
                break;
            case -2:
            case -6:
            case -7:
                $code = ECode::$MAIL_ERROR;
                break;
            case -3:
                $code = ECode::$MAIL_REJECT;
                break;
            case -4:
                $code = ECode::$MAIL_FULL;
                break;
            case -5:
                $code = ECode::$POST_FREQUENT;
                break;
            case -8:
                $code = ECode::$MAIL_RENUMERROR;
                break;
            case -9:
                $code = ECode::$MAIL_NOPERM;
                break;
            case -100:
                $code = ECode::$MAIL_NOID;
                break;
        }
        if(!is_null($code))
            throw new MailSendException($code);
    }

   /**
     * function forward mail id this mail to sb.
     *
     * @param string $target
     * @param boolean $noansi
     * @param boolean $big5
     * @return null
     * @access public
     */
    public function forward($target, $noansi = false, $big5 = false) {
        $code = null;
        $ret = bbs_domailforward($this->getFileName(), $this->TITLE, $target, $big5, $noansi);
        switch ($ret) {
            case -1:
            case -3:
                $code = ECode::$MAIL_FULL;
                break;
            case -2:
            case -5:
            case -6:
                $code = ECode::$MAIL_NOPERM;
                break;
            case -4:
                $code = ECode::$MAIL_REJECT;
                break;
            case -7:
                $code = ECode::$MAIL_NOMAIL;
                break;
            case -10:
                $code = ECode::$SYS_ERROR;
            case -201:
                $code = ECode::$MAIL_NOID;
                break;
        }
        if (!is_null($code))
            throw new MailSendException($code);
    }

    /**
     * function delete remove single mail
     *
     * @return boolean true|false
     * @access public
     * @override
     */
    public function delete(){
        $ret = bbs_delmail($this->_box->path, $this->FILENAME);
        if($ret != 0)
            return false;
        return true;
    }

    public function getFileName(){
        return bbs_setmailfile($this->_box->user->userid,$this->FILENAME);
    }

    public function getAttLink($pos){
        return "/{$this->_box->type}/{$this->num}/$pos";
    }

    public function getBox(){
        return $this->_box;
    }

    public function addAttach($file, $fileName){}
    public function delAttach($num){}
    public function isM(){
        return (strtolower($this->FLAGS[0]) == "m");
    }

    public function isRead(){
        return (strtolower($this->FLAGS[0]) != "n");
    }

    public function isReply(){
        return (strtolower($this->FLAGS[1]) == "r");
    }

    /**
     * function setRead set the mail read
     *
     * @return null
     * @access public
     */
    public function setRead(){
        bbs_setmailreaded($this->_box->getFullPath(), $this->num);
    }
}

class MailBoxNullException extends Exception {}
class MailDataNullException extends Exception {}
class MailNullException extends Exception {}
class MailSendException extends Exception {}
?>
