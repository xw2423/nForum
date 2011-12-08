<?php
class BasicAuth{
    public static function getCurrentUser(){
        if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
            header('WWW-Authenticate: Basic realm="nForum API"');
            header('HTTP/1.0 401 Unauthorized');
            exit();
        }
        $id = trim($_SERVER['PHP_AUTH_USER']);
        $pwd = $_SERVER['PHP_AUTH_PW'];
        if(strtolower($id) === 'guest' || Forum::checkPwd($id, $pwd, false, true))
            return $id;
        return false;
    }
}
?>
