<?php
class NF_Configure{
    public $loaded_files = array();
    public $config = array();
    private static $_instance = null;

    public static function getInstance(){
        if(null === self::$_instance)
            self::$_instance = new NF_Configure();
        return self::$_instance;
    }
}
if(!load(CONF . DS . 'nforum.php', NF_Configure::getInstance()->config, false))
    exit('can\'t find configure file in ' . CONF);

/**
 * function load
 *
 * load /path/to/file.php:
 *   load("/path/to/file(.php)");
 *
 * load LIB/file.php:
 *   load("file(.php)");
 *
 * load MODULE/$module/lib/file.php:
 *   load("$module.file(.php)")
 *
 * @param mixed $mixed
 * @param mixed $export
 */
function load($mixed, &$export = null, $cache = true){
    if(is_array($mixed)){
        $ret = true;
        foreach($mixed as $file)
            $ret &= load($file, $export);
        return $ret;
    }
    if($mixed[0] !== DS){
        if(false !== ($m = strstr($mixed, '.', true))
            && in_array(strtolower($m), c('modules.install'))){
                $mixed = MODULE . DS . ucfirst(strtolower($m)) . DS . 'lib' . DS . substr(strstr($mixed, '.'), 1);
        }else{
            $mixed = LIB . DS . $mixed;
        }
    }

    if(substr($mixed, -4, 4) !== '.php') $mixed = $mixed . '.php';

    $loaded = & NF_Configure::getInstance()->loaded_files;
    if(isset($loaded[$mixed])){
        $export = $loaded[$mixed];
        return true;
    }

    if(Yaf_Loader::import($mixed)){
        $loaded[$mixed] = ($cache && null !== $export) ? $export : true;
        return true;
    }
    return false;
}

function c($key, $val = null){
    $config = & NF_Configure::getInstance()->config;
    $k = explode('.', $key);
    if(null === $val){
        switch(count($k)){
            case 1:
                return isset($config[$k[0]])?$config[$k[0]]:null;
            case 2:
                return isset($config[$k[0]][$k[1]])?$config[$k[0]][$k[1]]:null;
            case 3:
                return isset($config[$k[0]][$k[1]][$k[2]])?$config[$k[0]][$k[1]][$k[2]]:null;
            case 4:
                return isset($config[$k[0]][$k[1]][$k[2]][$k[3]])?$config[$k[0]][$k[1]][$k[2]][$k[3]]:null;
            default:
                return null;
        }
    }else{
        switch(count($k)){
            case 1:
                $config[$k[0]] = $val;
                break;
            case 2:
                $config[$k[0]][$k[1]] = $val;
                break;
            case 3:
                $config[$k[0]][$k[1]][$k[2]] = $val;
                break;
            case 4:
                $config[$k[0]][$k[1]][$k[2]][$k[3]] = $val;
                break;
        }
    }
}

function dump(){
    ob_start();
    call_user_func_array('var_dump', func_get_args());
    $var = ob_get_clean();
    $var = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $var);
    if(defined('CLI'))
        echo $var;
    else
        echo <<<HTML
<pre style="
background-color:black;
font-size:12px;
border:1px solid white;
margin:0;
padding:10px;
word-break:break-all;
word-wrap:break-word;
color:white;
">
{$var}
</pre>
HTML;
}

function dump_backtrace(){
    ob_start();
    debug_print_backtrace();
    $var = ob_get_clean();
    if(defined('CLI'))
        echo $var;
    else
        echo <<<HTML
<pre style="
background-color:black;
font-size:12px;
border:1px solid white;
margin:0;
padding:10px;
word-break:break-all;
word-wrap:break-word;
color:white;
">
{$var}
</pre>
HTML;
}

function ip(){
    if(c('proxy.X_FORWARDED_FOR') && isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
        $ips = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
        if(trim($ips[0] !== ''))
            return trim($ips[0]);
    }
    return $_SERVER["REMOTE_ADDR"];
}

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
    $domain = isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'';
    $port = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80')?(':' . $_SERVER['SERVER_PORT']):'';

    if('' == c('cookie.domain'))
        c('cookie.domain', $domain);

    c('site.domain', 'http://' . $domain . $port);

    if('' == c('site.static'))
        c('site.static', 'http://' . $domain . $port);
    else{
        c('site.isStatic', $domain . $port == c('site.static'));
        c('site.static', 'http://' . c('site.static'));
    }

    foreach(c('modules.install') as $v){
        $d = c("modules.$v.domain");
        if($d === $domain . $port){
            c('site.base', '');
            c("modules.$v.base", '');
            c('modules.domain_module', $v);
            break;
        }
    }
}

/**
 * simple get & put file function
 */
function nforum_cache_write($file, $obj, $serialize = true){
    $dir = CACHE . DS . 'nforum';
    $file = $dir . DS . $file;
    if(!file_exists($dir) || !is_writeable($dir))
        return false;
    file_put_contents($file . 'tmp', serialize($obj));
    rename($file . 'tmp', $file);
}
function nforum_cache_read($file, $serialize = true){
    $file = CACHE . DS . 'nforum' . DS . $file;
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

/**
 * change encoding of string
 * @param string $from
 * @param string $to
 * @param mixed $in
 * @param int $param 0:'',1:'TRANSLIT',2:'IGNORE'
 * @return string
 */
function nforum_iconv($from , $to, $in, $param = 1){
    if(is_array($in)){
        foreach($in as &$v)
            $v = nforum_iconv($from, $to, $v, $param);
        return $in;
    }
    if(!is_string($in))
        return $in;
    $from = strtoupper($from);
    $to = strtoupper($to);
    if($from == $to)
        return $in;
    $charset = array('UTF-8', 'GBK', 'GB2312');
    $params = array('', '//TRANSLIT', '//IGNORE');
    $param = isset($params[$param])?$params[$param]:$params[1];
    if(!in_array($from, $charset) || !in_array($to, $charset))
        return $from;
    return @iconv($from , $to . $param, $in);
}

/**
 * redirect function
* if url is relative, redirect base on DOMAIN
 *
 * @param string $url
 * @param int $status
 * @param boolean $exit
 */
function nforum_redirect($url, $status = null, $exit = true){
    if(null !== $status){
        $codes = array(
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
        );
        if(isset($codes[$status]))
            header("HTTP/1.1 {$status} {$codes[$status]}");
    }

    if($url[0] === '/') $url = c('site.domain') . $url;

    header('Location:' . $url);
    if($exit) exit();
}

function nforum_cache($switch = false, $modified = 0, $expires = null){
    if($switch){
        if(is_null($expires))
            $expires = c('cache.second');
        if(!is_int($modified))
            $modified = 0;
        @$oldmodified = $_SERVER["HTTP_IF_MODIFIED_SINCE"];
        if ($oldmodified != "") {
            if (($pos = strpos($oldmodified, ';')) !== false)
                $oldmodified = substr($oldmodified, 0, $pos);
            $oldtime = strtotime($oldmodified);
        }else
            $oldtime = -1;
        if ($oldtime >= $modified){
            header("HTTP/1.1 304 Not Modified");
            header("Cache-Control: max-age=" . "$expires");
            exit();
        }
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $modified) . " GMT");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expires) . " GMT");
        header("Cache-Control: max-age=" . "$expires");
    }else{
        header("Expires: Thu, 18 Feb 1988 01:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
    }
}

/**
 * nforum error
 * show ajax via code or msg & stop
 *
 * @param mixed $mixed string|int
 * @param boolean $simple
 */
function nforum_error($mixed = null, $simple = false){
    if(null === $mixed)
        $mixed = ECode::$SYS_AJAXERROR;

    if($simple)
        exit(ECode::msg($mixed));

    throw new NF_ERRORException($mixed);
}

function nforum_error404($blank = false){
    if($blank){
        header('HTTP/1.1 404 Not Found');
        exit();
    }
    throw new NF_ERROR404Exception();
}

function nforum_compress_asset($request){
    $filter = array();

    $uri = $request->url;
    $ext = strrchr($uri, '.');

    if('.js' === $ext){
        $fileName = str_replace('/cjs/', '/js/', $uri);
        $type = substr($ext, 1);
    }else if('.css' === $ext){
        $fileName = str_replace('/ccss/', '/css/', $uri);
        $type = substr($ext, 1);
    }else{
        nforum_error404(true);
    }

    $file = WWW . $fileName;
    if(!file_exists($file)) nforum_error404(true);

    $fileModified = filemtime($file);
    nforum_cache(true, $fileModified, 2592000);

    $encoding = c('application.encoding');
    if($type === "css"){
        header("Content-Type: text/css;charset=$encoding");
    }else if($type === "js"){
        header("Content-Type: text/javascript;charset=$encoding");
    }
    foreach($filter as $v){
        if(strpos($file, $v) !== false){
            echo file_get_contents($file);
            exit();
        }
    }
    load('inc/packer');
    $p = new Packer();
    echo $p->pack($file, $type);
    exit();
}

function nforum_html($str){
    return htmlspecialchars($str, ENT_COMPAT | ( defined('ENT_HTML5') ? ENT_HTML5 : 0), 'ISO-8859-1');
}

class NF_Exception extends Exception{}
class NF_ERRORException extends Exception{}
class NF_ERROR404Exception extends Exception{}
