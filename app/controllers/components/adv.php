<?php
/**
 * Adv component for nforum
 * @author xw
 */
App::import("vendor", "inc/db");
class AdvComponent extends Object {
    public function initialize(&$controller) {
        $this->controller = $controller;
    }

    public function getBanner(){
        $db = DB::getInstance();
        $sql = "select url, file, remark from adv where type='2' and privilege=1";
        $res = $db->all($sql);
        if(empty($res)){
            $date = date('Y-m-d');
            $sql = "select url, file, remark from adv where type='2' and sTime<='$date' and eTime>='$date'";
            $res = $db->all($sql);
        }
        $aPath = Configure::read("adv.path");
        if(count($res) > 1) shuffle($res);
        foreach($res as &$v){
            $v['file'] = '/' . $aPath . "/" . $v['file'];
        }
        return $res;
    }

    public function getLeft(){
        $db = DB::getInstance();
        $sql = "select url, file, remark from adv where type='4' and switch='1' order by weight,aid desc";
        $aPath = Configure::read("adv.path");
        $ret = $db->all($sql);
        foreach($ret as &$v){
            $v['file'] = '/' . $aPath . "/" . $v['file'];
        }
        return $ret;
    }

    public function getPreImg(){
        $db = DB::getInstance();
        $sql = "select url, file, remark from adv where type='1' and privilege=1 ";
        $res = $db->all($sql);
        if(empty($res)){
            $date = date('Y-m-d');
            $sql = "select url,file from adv where type='1' and sTime<='$date' and eTime>='$date'";
            $res = $db->all($sql);
        }
        if(empty($res))
            return array();
        $ret = $res[array_rand($res)];
        $aPath = Configure::read("adv.path");
        $base = Configure::read("site.prefix");
        $ret['file'] = '/' . $aPath . "/" . $ret['file'];
        if($ret['url'] == "")
            $ret['url'] = $base . Configure::read("site.home");
        return $ret;
    }

    public function getPreAdv(){
        $db = DB::getInstance();
        $sql = "select url,file,remark from adv where type='3' and switch=1 order by weight,aid desc limit 5";
        $ret = $db->all($sql);
        if(empty($ret))
            return array();
        $aPath = Configure::read("adv.path");
        foreach($ret as &$v){
            $v['file'] = '/' . $aPath . "/" . $v['file'];
        }
        return $ret;
    }
}
?>
