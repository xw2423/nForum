<?php
if (!defined('CAKE_CORE_INCLUDE_PATH')) {
    error404();
}
$filter = array();
if (!preg_match("/plugins\/([^\/]+)\/c(css)\/([^\/]+\.css)$/i", $url, $match)
    && !preg_match("/plugins\/([^\/]+)\/c(js)\/([^\/]+\.js)$/i", $url, $match)
    && !preg_match("/c(css)\/([^\/]+\.css)$/i", $url, $match)
    && !preg_match("/c(js)\/([^\/]+\.js)$/i", $url, $match)){
    error404();
}
if(isset($match[3])){
    $fileName = "plugins" . DS . $match[1] . DS . $match[2] . DS . $match[3];
    $type = $match[2];
}else{
    $fileName = $match[1] . DS . $match[2];
    $type = $match[1];
}
$file = WWW_ROOT . $fileName;
if(!file_exists($file)){
    error404();
}
$fileModified = filemtime($file);
make_cache(true, $fileModified, 259200);
$encoding = Configure::read("App.encoding");
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
APP::import('vendor', 'inc/packer');
$p = new Packer();
echo $p->pack($file, $type);

function make_cache($switch = false, $modified = 0, $expires = null){
    if($switch){
        if(is_null($expires))
            $expires = 300;
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
        header("Expires: Tue, 18 Nov 1988 09:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
    }
}

function error404(){
    header('HTTP/1.1 404 Not Found');
    exit();
}
?>
