<?php
/****************************************************
 * FileName: app/vendors/inc/ubb.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/

/**
 * class XUBB
 *
 * @author xw
 */
class XUBB {
    public static $rule = array(
        array(
            "'\[ATT=([^\[\]]*?) SIZE=([^\[\]]*?)\](?:\s*?)\[/ATT\]'",
            "'\[(em[a-zA-Z]?)0?(\d+)\]'i",
            "'\[b\](.*?)\[/b\]'i",
            "'\[i\](.*?)\[/i\]'i",
            "'\[u\](.*?)\[/u\]'i",
            //compact for wForum tag, move,fly,glow,shadow
            "'\[move\](.*?)\[/move\]'i",
            "'\[fly\](.*?)\[/fly\]'i",
            "'\[glow=(?:[^,]*?),([^,]*?),([^,]*?)\](.*?)\[/glow\]'i",
            "'\[shadow=(?:[^,]*?),([^,]*?),([^,]*?)\](.*?)\[/shadow\]'i",
            "'\[color=(#\w*?)\](.*?)\[/color\]'i",
            "'\[size=(\d*?)\](.*?)\[/size\]'i",
            "'\[face=([^\[\]<>]{1,16})\](.*?)\[/face\]'i",
            "'\[url=((?:http|https|ftp|rtsp|mms)[^\[\]\"\']*?)\](.*?)\[/url\]'i",
            "'\[email=((?:[a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+@(?:[a-zA-Z0-9)]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3})\](.*?)\[/email\]'i",
            "'\[img=((?:http|https)[^\[\]\"\']*?)\](?:\s*?)\[/img\]'i",
            "'\[swf=((?:http|https)[^\[\]\"\']*?)\](?:\s*?)\[/swf\]'i",
            "'\[mp3=((?:http|https)[^\[\]\"\']*?) auto=([01])\](?:\s*?)\[/mp3\]'i",
            "'\[map=([0-9,.{}]*?) mark=([0-9,.{}]*?)\](?:\s*?)\[/map\]'i",
            "'(?<!>|=|\")(?:http|https|ftp|rtsp|mms):(?://|\\\\)(&(?=amp;)|[A-Za-z0-9\./=\?%\-#_~`@\[\]\':;+!])+'i"
        ),
        array(
            "<font color=\"blue\">附件(\\2字节):</font>&nbsp;<a href=\"%base%\\1\">\\3</a>",
            "<img src=\"%base%/img/ubb/\\1/\\2.gif\" style=\"display:inline;border-style:none\"/>",
            "<b>\\1</b>",
            "<i>\\1</i>",
            "<u>\\1</u>",
            //compact for wForum tag, move,fly,glow,shadow,glow and shadow use css3
            "<marquee scrollamount=\"3\">\\1</marquee>",
            "<marquee width=\"100%\" behavior=\"alternate\" scrollamount=\"3\">\\1</marquee>",
            "<span style=\"text-shadow:0px 0px \\2px \\1\">\\3</span>",
            "<span style=\"text-shadow:2px 2px \\2px \\1\">\\3</span>",
            "<font color=\"\\1\">\\2</font>",
            "<font size=\"\\1\">\\2</font>",
            "<font face=\"\\1\">\\2</font>",
            "<a target=\"_blank\" href=\"\\1\">\\2</a>",
            "<a target=\"_blank\" href=\"mailto:\\1\">\\2</a>",
            "<a target=\"_blank\" href=\"\\1\"><img border=\"0\" title=\"单击此在新窗口浏览图片\" src=\"\\1\" class=\"resizeable\" /></a>",
            "<div class=\"a-swf\" _src=\"\\1\"><object class=\"resizeable\"classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0\" width=\"560px\" height=\"420px\"><param name=\"allowScriptAccess\" value=\"never\" /><param name=\"allowFullScreen\" value=\"true\" /><param name=\"movie\" value=\"\\1\" /><param name=\"quality\" value=\"high\" /><embed src=\"\\1\" quality=\"high\" allowScriptAccess=\"never\" allowFullScreen=\"true\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" width=\"560px\" height=\"420px\"/></object></div>",
            "<div class=\"a-audio\" _src=\"\\1\" _auto=\"\\2\"></div>",
            "<div _bound=\"\\1\"_mark=\"\\2\" class=\"map-map\"></div>",
            "<a target=\"_blank\" href=\"\\0\">\\0</a>"
        ),
        array(
            "",
            "",
            "\\0",
            "\\0",
            "\\0",
            "\\1",
            "\\1",
            "\\3",
            "\\3",
            "\\0",
            "\\2",
            "\\1",
            "\\2",
            "\\2",
            "",
            "",
            "",
            "",
            "\\0",
        ),
    );

    public static function parse($text){
        $syn = Configure::read('ubb.syntax');
        if(!empty($syn)){
            self::$rule[0][] = "'\[code=(\w*?)\]([\s\S]*?)\[/code\]'e";
            self::$rule[1][] = "self::parseCode('\\1', '\\2')";
            self::$rule[2][] = "\\2";
        }
        $prefix = Configure::read('site.prefix');
        return str_replace('%base%', $prefix, preg_replace(self::$rule[0], self::$rule[1], $text));
    }

    public static function remove($text){
        return preg_replace(self::$rule[0], self::$rule[2], $text);
    }

    public static function parseCode($lang, $code){
        //preg_match use 'e' quote "?
        $code = str_replace("\\\"", "\"", $code);
        $code = preg_replace("'<br ?/?>'", "\n", $code);
        $code = str_replace("&nbsp;", " ", $code);
        $code = preg_replace("'</?[^>]*?>'", "", $code);
        $code = trim($code);
        $code = "<pre class=\"brush:$lang\">$code</pre>";
        return $code;
    }
}
?>
