<?php
App::import("vendor", array("inc/pagination", "inc/db"));
class VoteList implements Pageable{

    private $_sql = "";
    private $_param = null; 
    private $_num = null; 
    
    public function __construct($sql, $param = null){
        $this->_sql = trim($sql);
        $this->_param = $param;
    }

    public function getTotalNum(){
        if(is_null($this->_num)){
            $sql = preg_replace("/^select.*?from/", "select count(*) as num from", $this->_sql);
            $db = DB::getInstance();
            $res = $db->one($sql, $this->_param);
            $this->_num = ($res === false)?0:$res['num'];
        }
        return $this->_num;
    }

    public function getRecord($start, $num){
        $sql = $this->_sql . " limit " . ($start - 1) . ",$num";
        $db = DB::getInstance();
        $res = $db->all($sql);
        $ret = array();
        foreach($res as $v){
            $ret[] = new Vote($v['vid'], $v);
        }
        return $ret;
    }
}

class Vote{
    private $_vid = null;
    private $_info = null;
    private $_items = null;
    private $_total = null;
    private $_viids = null;

    public static function add($uid, $subject, $desc, $end, $type, $limit,  $items, $result_voted = 0){
        $db = DB::getInstance();    
        $val = array("k"=>array('uid', 'subject', 'desc', 'start', 'end', 'type', 'limit', 'status', 'result_voted'),
            "v" => array(array($uid, $subject, $desc, time(), $end, $type, $limit, 1, $result_voted)));
        $db->insert('pl_vote', $val);
        $vid = $db->lastInsertId();
        $vars = array();
        foreach($items as $v){
            $vars[] = array($vid, $v);
        }
        $val = array("k"=>array('vid', 'label'), "v" => $vars);
        $db->insert('pl_vote_item', $val);
        return $vid;
    }

    public function __construct($vid, $info = null){
        if(is_array($info)){
            $this->_info = $info;
            $this->_vid = $info['vid'];
        } else{
            $this->_vid = $vid;
            $this->_initInfo();
        }
    }

    public function __get($name){
        if($name == "items"){
            $this->_initItem();
            return $this->_items;
        }
        if($name == "total"){
            $this->_initTotal();
            return $this->_total;
        }
        if (array_key_exists($name, $this->_info)) {
            return $this->_info[$name];
        }
        return null;
    }

    public function getResult($uid){
        $sql = "select result,time from pl_vote_result where vid=? and uid=? limit 1"; 
        $db = DB::getInstance();
        $res = $db->one($sql, array($this->_vid, $uid));
        if($res === false)
            return false;
        $items = $this->items;
        $voted = explode("|", $res['result']);
        $ret = array('time'=>$res['time'], "items"=>$voted);
        foreach($items as $v){
            if(in_array($v['viid'], $voted))
                $ret['labels'][] = $v['label'];
        }
        return $ret;
    }

    public function hasItem($viid){
        if(is_null($this->_viids)){
            foreach($this->items as $v){
                $this->_viids[] = $v['viid'];
            }
        }
        return in_array(strval($viid), $this->_viids);
    }

    /**
     * @param string $uid
     * @param mixed $item  string for single,array for multi
     */
    public function vote($uid, $items){
        $db = DB::getInstance();
        if($this->type == 0){
            $items = strval($items);
            $val = array("\\num"=>"num+1");
            $db->update("pl_vote", $val, "where vid=?", array($this->vid));
            $db->update("pl_vote_item", $val, "where viid=?", array($items));
            $val = array("k"=>array('vid', 'uid', 'result', 'time'),"v"=>array(array($this->vid, $uid, $items, time())));
            $db->insert("pl_vote_result", $val);
        }else if($this->type == 1){
            if(!is_array($items))
                return;
            $val = array("\\num"=>"num+1");
            $db->update("pl_vote", $val, "where vid=?", array($this->vid));
            foreach($items as $v){
                $db->update("pl_vote_item", $val, "where viid=?", array($v));
            }
            $val = array("k"=>array('vid', 'uid', 'result', 'time'),"v"=>array(array($this->vid, $uid, join("|", $items), time())));
            $db->insert("pl_vote_result", $val);
        }
    }

    public function delete(){
        $db = DB::getInstance();
        $val = array("status"=>"0");
        $db->update("pl_vote", $val, "where vid=?", array($this->vid));
    }

    public function isDeleted(){
        return $this->status == "0";
    }
    
    public function isEnd(){
        return $this->end < (time() - 86400);
    }

    private function _initInfo(){
        $sql = "select * from pl_vote where vid=? limit 1"; 
        $db = DB::getInstance();
        $this->_info = $db->one($sql, array($this->_vid));
        if($this->_info === false)
            throw new VoteNullException();
    }

    private function _initItem(){
        if(!is_null($this->_items))
            return;
        $sql = "select viid,label,num from pl_vote_item where vid=?"; 
        $db = DB::getInstance();
        $this->_items = $db->all($sql, array($this->_vid));
    }

    private function _initTotal(){
        if(!is_null($this->_total))
            return;
        $sql = "select sum(num) as total from pl_vote_item where vid=?"; 
        $db = DB::getInstance();
        $res = $db->one($sql, array($this->_vid));
        $this->_total = ($res === false)?0:intval($res['total']);
    }
}
class VoteNullException extends Exception {}
?>
