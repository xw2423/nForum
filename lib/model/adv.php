<?php
load(array("inc/pagination", "inc/db"));

/**
 * class Adv
 * it is base on database
 *
 * @implements Pageable
 * @author xw
 */
class Adv implements Pageable {

    public $type = 1;
    public $sid = "aid";
    public $sorder = "asc";

    public $search = null;
    public $search_start = null;
    public $search_end = null;

    private $_table = "adv";
    private $_num = null;
    private $_sql = null;

    public function getTotalNum(){
        $where = '';
        if(null !== $this->search)
            $where .= " and remark like ?";
        if(null !== $this->search_start && preg_match("/\d{4}-\d\d-\d\d/", $this->search_start))
            $where .= " and sTime>='{$this->search_start}'";
        if(null !== $this->search_end && preg_match("/\d{4}-\d\d-\d\d/", $this->search_end))
            $where .= " and eTime<='{$this->search_end}'";
        $sql = "select count(*) as num from {$this->_table} where type='{$this->type}' $where";
        if(is_null($this->_num) || $sql !== $this->_sql){
            $db = DB::getInstance();
            $this->_num = $db->one($sql,  array('%' . $this->search . '%'));
            $this->_num = $this->_num['num'];
        }
        return $this->_num;
    }

    public function getRecord($start, $num){
        $where = '';
        if(null !== $this->search)
            $where .= " and remark like ?";
        if(null !== $this->search_start && preg_match("/\d{4}-\d\d-\d\d/", $this->search_start))
            $where .= " and sTime>='{$this->search_start}'";
        if(null !== $this->search_end && preg_match("/\d{4}-\d\d-\d\d/", $this->search_end))
            $where .= " and eTime<='{$this->search_end}'";
        if(1 == $this->type || 2 == $this->type){
            $date = date('Y-m-d');
            $sql = "select *,(sTime<='$date' and eTime>='$date') as used from adv where type='{$this->type}' $where order by privilege desc,(sTime<='$date' and eTime>='$date') desc, sTime desc,aid desc limit " . ($start - 1). ",$num";
        }else{
            $sql = "select * from adv where type='{$this->type}' $where order by switch desc,weight,sTime desc,aid desc";
        }
        $db = DB::getInstance();
        $ret = $db->all($sql, array('%' . $this->search . '%'));
        return $ret;
    }

    public function update($aid, $url, $sTime, $eTime, $switch, $weight, $privilege, $remark){
        $val = array(
            "url" => $url,
            "sTime" => $sTime,
            "eTime" => $eTime,
            "switch" => $switch,
            "weight" => $weight,
            "privilege" => $privilege,
            "remark" => $remark
        );
        $where = "where aid='$aid'";
        $db = DB::getInstance();
        $db->update($this->_table, $val, $where);
    }

    public function delete($aid){
        $db = DB::getInstance();
        $ret = $db->one("select file from {$this->_table} where aid='$aid'");
        $where = "where aid='$aid'";
        $db->delete($this->_table,$where);
        return $ret['file'];
    }

    public function add($type, $file, $url, $sTime, $eTime, $switch, $weight, $privilege, $remark){
        $val = array('type' => $type, 'file' => $file, 'url' => $url, 'sTime' => $sTime , 'eTime' => $eTime
            , 'switch' => $switch, 'weight' => $weight, 'privilege' => $privilege, 'remark' => $remark);
        $db = DB::getInstance();
        $db->insert($this->_table, $val);
    }

    public static function getBanner(){
        $db = DB::getInstance();
        $sql = "select url, file, remark from adv where type='2' and privilege=1";
        $res = $db->all($sql);
        if(empty($res)){
            $date = date('Y-m-d');
            $sql = "select url, file, remark from adv where type='2' and sTime<='$date' and eTime>='$date'";
            $res = $db->all($sql);
        }
        $aPath = c("adv.path");
        if(count($res) > 1) shuffle($res);
        foreach($res as &$v){
            $v['file'] = $aPath . "/" . $v['file'];
        }
        return $res;
    }

    public static function getLeft(){
        $db = DB::getInstance();
        $sql = "select url, file, remark from adv where type='4' and switch='1' order by weight,aid desc";
        $aPath = c("adv.path");
        $ret = $db->all($sql);
        foreach($ret as &$v){
            $v['file'] = $aPath . "/" . $v['file'];
        }
        return $ret;
    }

    public static function getPreImg(){
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
        $aPath = c("adv.path");
        $base = c("site.base");
        $ret['file'] = $aPath . "/" . $ret['file'];
        if($ret['url'] == "")
            $ret['url'] = $base . c("site.home");
        return $ret;
    }

    public static function getPreAdv(){
        $db = DB::getInstance();
        $sql = "select url,file,remark from adv where type='3' and switch=1 order by weight,aid desc limit 5";
        $ret = $db->all($sql);
        if(empty($ret))
            return array();
        $aPath = c("adv.path");
        foreach($ret as &$v){
            $v['file'] = $aPath . "/" . $v['file'];
        }
        return $ret;
    }
}
