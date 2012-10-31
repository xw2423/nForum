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
        $key = array("type", "file", "url", "sTime", "eTime", "switch", "weight", "privilege", "remark");
        $val = array(array($type, $file, $url, $sTime, $eTime, $switch, $weight, $privilege, $remark));
        $db = DB::getInstance();
        $db->insert($this->_table, array("k"=>$key, "v"=>$val));
    }
}
?>
