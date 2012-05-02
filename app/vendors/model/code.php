<?php
/****************************************************
 * FileName: app/vendors/model/code.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
/**
 * class ECode contain all the message in nforum
 *
 * @author xw
 */
class ECode {
    public static $SYS_ERROR=            '0000';
    public static $SYS_INDEX=            '0001';
    public static $SYS_NOLOGIN=          '0002';
    public static $SYS_NOTMPFILE=        '0003';
    public static $SYS_REQUESTERROR=     '0004';
    public static $SYS_AJAXOK=           '0005';
    public static $SYS_AJAXERROR=        '0006';
    public static $SYS_NOFILE=           '0007';
    public static $SYS_IPBAN=            '0008';
    public static $SYS_PLUGINBAN=        '0009';
    public static $SYS_404=              '0010';
    public static $LOGIN_NOID =          '0100';
    public static $LOGIN_ERROR =         '0101';
    public static $LOGIN_MULLOGIN =      '0102';
    public static $LOGIN_IDBAN =         '0103';
    public static $LOGIN_IPBAN =         '0104';
    public static $LOGIN_FREQUENT =      '0105';
    public static $LOGIN_MAX =           '0106';
    public static $LOGIN_EPOS =          '0107';
    public static $LOGIN_OK =            '0108';
    public static $LOGIN_OUT =           '0109';
    public static $BOARD_NONE=           '0200';
    public static $BOARD_UNKNOW =        '0201';
    public static $BOARD_NOPERM =        '0202';
    public static $BOARD_READONLY =      '0203';
    public static $BOARD_NOPOST =        '0204';
    public static $BOARD_NOREPLY =       '0205';
    public static $BOARD_NOTHREADS =     '0206';
    public static $BOARD_VOTEFAIL =      '0207';
    public static $BOARD_VOTESUCCESS =   '0208';
    public static $BOARD_MODEERROR =     '0209';
    public static $ARTICLE_NONE =        '0300';
    public static $ARTICLE_NOREID =      '0301';
    public static $ARTICLE_NOREPLY =     '0302';
    public static $ARTICLE_NODEL =       '0303';
    public static $ARTICLE_NOEDIT =      '0304';
    public static $ARTICLE_EDITERROR =   '0305';
    public static $ARTICLE_EDITOK =      '0306';
    public static $ARTICLE_DELOK =       '0307';
    public static $ARTICLE_REERROR =     '0308';
    public static $ARTICLE_REOK =        '0309';
    public static $ARTICLE_FORWARDOK =   '0310';
    public static $ARTICLE_NOMANAGE =    '0311';
    public static $ARTICLE_NOTORIGIN =   '0312';
    public static $POST_NOSUB =          '0400';
    public static $POST_NOCON =          '0401';
    public static $POST_ISDIR =          '0402';
    public static $POST_FREQUENT =       '0403';
    public static $POST_BAN =            '0404';
    public static $POST_WAIT =           '0405';
    public static $POST_OK =             '0406';
    public static $TMPL_ERROR =          '0407';
    public static $USER_NOID =           '0500';
    public static $USER_FAVERROR =       '0501';
    public static $USER_EWIDTH =         '0502';
    public static $USER_EHEIGHT =        '0503';
    public static $USER_SAVEOK =         '0504';
    public static $USER_NAMEOK =         '0505';
    public static $USER_NAMEERROR =      '0506';
    public static $USER_PWDOK =          '0507';
    public static $USER_PWDERROR =       '0508';
    public static $USER_OLDPWDERROR =    '0509';
    public static $MAIL_NOBOX =          '0600';
    public static $MAIL_NOMAIL =         '0601';
    public static $MAIL_SENDERROR =      '0602';
    public static $MAIL_ERROR =          '0603';
    public static $MAIL_REJECT =         '0604';
    public static $MAIL_FULL =           '0605';
    public static $MAIL_NOID =           '0606';
    public static $MAIL_SENDOK =         '0607';
    public static $MAIL_DELETEERROR =    '0608';
    public static $MAIL_DELETEOK =       '0609';
    public static $MAIL_RENUMERROR =     '0610';
    public static $MAIL_NOPERM =         '0611';
    public static $MAIL_FORWARDOK =      '0612';
    public static $SEC_NOSECTION =       '0700';
    public static $SEC_NOHOT =           '0701';
    public static $SEC_NOBOARD =         '0702';
    public static $FRIEND_NOPRIV =       '0800';
    public static $FRIEND_EXIST =        '0801';
    public static $FRIEND_NOEXIST =      '0802';
    public static $FRIEND_ADDOK =        '0803';
    public static $FRIEND_DELETEOK =     '0804';
    public static $ELITE_NODIR =         '0900';
    public static $ELITE_DIRERROR =      '0901';
    public static $RESET_QUICKNOID =     '1000';
    public static $RESET_OK =            '1001';
    public static $RESET_NONUM =         '1002';
    public static $BIND_PWDERROR =       '1003';
    public static $BIND_PHONEERROR =     '1004';
    public static $BIND_OK =             '1005';
    public static $BIND_ERROR =          '1006';
    public static $P2S_ERROR =           '1007';
    public static $P2S_OK =              '1008';
    public static $REG_FORMAT =          '1100';
    public static $REG_IDUSED =          '1101';
    public static $REG_PWD =             '1102';
    public static $REG_AUTH =            '1103';
    public static $REG_OK =              '1104';
    public static $REG_FORMOK =          '1105';
    public static $REG_REGED =           '1106';
    public static $REG_HAVAFORM =        '1107';
    public static $ATT_NLIMIT =          '1201';
    public static $ATT_SLIMIT =          '1202';
    public static $ATT_NONE =            '1203';
    public static $ATT_NAMEERROR =       '1204';
    public static $ATT_SAMENAME =        '1205';
    public static $ATT_NOPERM =          '1206';
    public static $ATT_ADDOK =           '1207';
    public static $ATT_DELOK =           '1208';
    public static $REFER_NONE =          '1301';
    public static $REFER_DELETEOK =      '1302';
    public static $REFER_DISABLED =      '1303';
    public static $DENY_DENIED =         '1401';
    public static $DENY_NOTDENIED =      '1402';
    public static $DENY_INVALIDDAY =     '1403';
    public static $DENY_NOREASON =       '1404';
    public static $DENY_CANTPOST =       '1405';
    public static $DENY_NOID  =          '1406';

    //no use just remember some time
    public static $XW_JOKE =             '9999';

    public static function msg($code){
        $msg = Configure::read("code.$code");
        return empty($msg)?$code:$msg;
    }

    /**
     * function kbs2code
     * kbs php extension has defined some code 
     * this function change it to nforum code
     *
     * @param int $kbscode
     * @return string
     * @static
     * @access public
     * @see php/phpbbs_errorno.h in kbs
     */
    public static function kbs2code($kbscode){
        switch($kbscode){
            case -100:
                return self::$SYS_ERROR;
            case -101:
            case -102:
                return self::$BOARD_UNKNOW;
            case -103:
                return self::$POST_BAN;
            case -104:
                return self::$BOARD_NOPOST;
            case -105:
                return self::$BOARD_READONLY;
            case -106:
                return self::$ARTICLE_NOEDIT;
            case -201:
                return self::$USER_NOID;
            case -301:
                return self::$ARTICLE_NOEDIT;
            case -302:
                return self::$ARTICLE_NONE;
            case -901:
                return self::$ATT_NLIMIT;
            case -902:
                return self::$ATT_SLIMIT;
            case -903:
                return self::$ATT_NONE;
            case -904:
                return self::$ATT_NAMEERROR;
            case -905:
                return self::$ATT_SAMENAME;
            case -906:
                return self::$ATT_NOPERM;
            default:
                return self::$SYS_ERROR;
        }
    }
}
?>
