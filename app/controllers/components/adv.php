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

    //called after Controller::beforeRender()
    public function beforeRender(&$controller) {
        $this->controller->set("advParam", $this->getParams());
        if(!$this->controller->brief)
            $this->controller->set("advs", $this->getLeft());
    }

    public function getParams(){
        $db = DB::getInstance();
        $sql = "select url, file from adv where type='2' and privilege=1 order by aid";
        $res = $db->all($sql);
        if(empty($res)){
            $sql = "select url, file from adv where type='2' and sTime<=CURRENT_DATE and eTime>=CURRENT_DATE order by aid";
            $res = $db->all($sql);
        }
        $url = $path = array();
        $aPath = Configure::read("adv.path");
        $base = Configure::read("site.prefix");
        $static = Configure::read("site.static");
        if(empty($res))
            return "";
        shuffle($res);
        foreach($res as $v){
            $url[] = urlencode($v['url']);
            $path[] = $static . $base . '/' . $aPath . "/" . $v['file'];
        }
        return "pics=" . join('|', $path) . "&links=" . join('|', $url);
    }

    public function getLeft(){
        $db = DB::getInstance();
        $sql = "select url, file from adv where type='4' and switch='1' order by weight,aid";
        $res = array();
        $aPath = Configure::read("adv.path");
        $base = Configure::read("site.prefix");
        $static = Configure::read("site.static");
        $ret = $db->all($sql);
        if(empty($ret))
            return array();
        foreach($ret as $v){
            if($v['url'] == "")
                $v['url'] = "javascript:void(0);";
            $res[] = array('url' => $v['url'], 'path' => $static . $base . '/' . $aPath . "/" . $v['file']);
        }
        return $res;
    }

    public function getPreImg(){
        $db = DB::getInstance();
        $sql = "select url,file from adv where type='1' and sTime<=CURRENT_DATE and eTime>=CURRENT_DATE";
        $res = $db->all($sql);
        if(empty($res))
            return array();
        $ret = $res[array_rand($res)];
        $aPath = Configure::read("adv.path");
        $ret['file'] = '/' . $aPath . "/" . $ret['file'];
        if($ret['url'] == "")
            $ret['url'] = Configure::read("site.home");
        return $ret;
    }

    public function getPreAdv(){
        $db = DB::getInstance();
        $sql = "select url,file from adv where type='3' and switch=1";
        $res = $db->all($sql);
        if(empty($res))
            return array();
        if(count($res) > 4)
            $select = array_rand($res, 4);
        else
            $select = range(0, count($res) - 1);
        $aPath = Configure::read("adv.path");
        $base = Configure::read("site.prefix");
        foreach($select as $k){
            $tmp = $res[$k];
            $tmp['file'] = $base . '/' . $aPath . "/" . $tmp['file'];
            if($tmp['url'] == "")
                $tmp['url'] = "javascript:void(0);";
            $ret[] = $tmp;
        }
        return $ret;
    }
}
?>
