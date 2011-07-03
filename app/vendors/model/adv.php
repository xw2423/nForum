<?php
/****************************************************
 * FileName: app/vendors/model/adv.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
App::import("vendor", array("inc/pagination", "inc/db"));

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

    private $_table = "adv";
    private $_num = null;
    private $_sql = null;

    public function getTotalNum(){
        $sql = "select * from {$this->_table} where type='{$this->type}'";
        if(is_null($this->_num) || $sql !== $this->_sql){
            $db = DB::getInstance();
            $this->_num = $db->query($sql)->rowCount();
        }
        return $this->_num;
    }

    public function getRecord($start, $num){
        $sql = "select * from {$this->_table} where type='{$this->type}' order by switch desc,weight,sTime desc,aid desc limit " . ($start - 1). ",$num";
        $db = DB::getInstance();
        $ret = $db->all($sql);
        return $ret;
    }

    public function update($aid, $url, $sTime, $eTime, $switch, $weight, $privilege, $home, $remark){
        $val = array(
            "url" => $url,
            "sTime" => $sTime,
            "eTime" => $eTime,
            "switch" => $switch,
            "weight" => $weight,
            "privilege" => $privilege,
            "home" => $home,
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

    public function add($type, $file, $url, $sTime, $eTime, $switch, $weight, $privilege, $home, $remark){
        $key = array("type", "file", "url", "sTime", "eTime", "switch", "weight", "privilege", "home", "remark");
        $val = array(array($type, $file, $url, $sTime, $eTime, $switch, $weight, $privilege, $home, $remark));
        $db = DB::getInstance();
        $db->insert($this->_table, array("k"=>$key, "v"=>$val));
    }
}
?>
