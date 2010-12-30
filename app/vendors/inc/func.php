<?php
/****************************************************
 * FileName: app/vendors/inc/func.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/

/**
 * fix the wrong gb2312 word
 * @param string $txt
 * @return string
 */
function nforum_fix_gbk($txt){
    $p = "/^(([".chr(0x81)."-".chr(0xff)."][".chr(0x40)."-".chr(0xff)."])*|[".chr(0x1)."-".chr(0x7e)."]*)+$/";
    if(!preg_match($p, $txt))
        return substr($txt, 0, strlen($txt) - 1);
    return $txt;
}

/**
 * check ipv6
 * @param string $ip
 * @return boolean true|false
 */
function nforum_is_ipv6($ip){
    return !(strpos($ip, ':') === false);
}

/**
 * valid current domain
 * reset static domain & cookie domain
 */
function nforum_check_domain(){
    $cur = isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:"";
    $cur_p = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != "80")?(":" . $_SERVER['SERVER_PORT']):"";
    $domain = Configure::read("site.domain");
    if(is_array($domain)){
        if(in_array($cur . $cur_p, $domain))
            $domain = $cur . $cur_p;
        else
            $domain = $domain[0];
    }
    if("" == Configure::read("cookie.domain")){
        Configure::write("cookie.domain", str_replace($cur_p, '', $domain));
    }
    $ssl = Configure::read("site.ssl");
    $domain = ($ssl?"https://":"http://") . $domain;
    Configure::write("site.domain", $domain);
    if("" == Configure::read("site.static")){
        Configure::write("site.static", $domain);
    }
}

/**
 * simple get & put file function
 */
function nforum_cache_write($file, $obj, $serialize = true){
    $dir = CACHE . "nforum";
    $file = $dir . DS . $file;
    if(!file_exists($dir) || !is_writeable($dir))
        return false;
    file_put_contents($file, serialize($obj));
}
function nforum_cache_read($file, $serialize = true){
    $file = CACHE . "nforum" . DS . $file;
    if(!file_exists($file) || !is_readable($file))
        return false;
    return unserialize(file_get_contents($file));
}

/**
 * change size to a readable format
 * @param int|string $num
 * @return string
 */
function nforum_size_format($num){
    $sizes = array(
        //'YB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024,
        //'ZB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024,
        //'EB' => 1024 * 1024 * 1024 * 1024 * 1024 * 1024,
        //'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
        'TB' => 1024 * 1024 * 1024 * 1024,
        'GB' => 1024 * 1024 * 1024,
        'MB' => 1024 * 1024,
        'KB' => 1024
    );
    if($num < 1024)
        return $num . 'B';
    foreach($sizes as $k=>$v){
        if($num >= $v){
            $num = round($num / $v, 1) . $k;
            break;
        }
    }
    return $num;
}

?>
