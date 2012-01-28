<?php
/****************************************************
 * FileName: app/vendors/model/refer.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
App::import("vendor", "model/article");

/**
 * class Refer
 *
 * $refer_array = array (
 *     "INDEX"     refer index
 *     "BOARD":    board name
 *     "USER":     user id
 *     "TITLE":    article title
 *     "ID":       article id
 *     "GROUP_ID": article groupid
 *     "RE_ID":    article reid
 *     "FLAG":     some flags for refer:
 *                 0x01: read
 *     "TIME":     timestamp, refer time
 * );
 *
 * @implements Pageable
 * @author xw
 */
class Refer implements Pageable{
    public static $AT = "at";
    public static $REPLY = "reply";
    public static $FLAG_READ = 0x01;

    private $_type = "at";
    private $_types = array("at" => 1, "reply" => 2);
    private $_descs = array("at" => "提到我的文章", "reply" => "回复我的文章");

    private $_newNum = 0;
    private $_totalNum = 0;
    private $_user;
    private $_init = false;

    public function __construct($user, $type) {
        if(!in_array($type, array_keys($this->_types)))
            throw new ReferNullException();
        $this->_type = $type;
        $this->_user = $user;
    }

    public function getTotalNum(){
        $this->_initNum();
        return $this->_totalNum;
    }

    public function getRecord($start, $num){
        $this->_initNum();
        $tmp = array();
        if(bbs_load_refer($this->_user->userid, $this->_types[$this->_type], $this->_totalNum - $start + 1 - $num, $num, &$tmp) > 0){
            foreach($tmp as $k=>&$v){
                $v['INDEX'] = $this->_totalNum - $start + 1 - $num + $k;
            }
            return array_reverse($tmp);
        }
        return array();
    }

    public function getType(){
        return $this->_type;
    }

    public function getDesc(){
        return $this->_descs[$this->_type];
    }

    public function getRefer($index){
        return $this->getRecord($index + 1, 1);
    }

    public function getNewNum(){
        $this->_initNum();
        return $this->_newNum;
    }

    public function setRead($index = false){
        if(false === $index)
            bbs_read_all_refer($this->_user->userid, $this->_types[$this->_type]);
        else
            bbs_read_refer($this->_user->userid, $this->_types[$this->_type], $index);
    }

    public function delete($index = false){
        if(false === $index)
            bbs_truncate_refer($this->_user->userid, $this->_types[$this->_type]);
        else
            bbs_delete_refer($this->_user->userid, $this->_types[$this->_type], $index);
    }

    private function _initNum(){
        if(!$this->_init){
            bbs_get_refer($this->_user->userid, $this->_types[$this->_type], &$this->_totalNum, &$this->_newNum);
            $this->_init = true;
        }
    }
}
class ReferNullException extends Exception {}
?>
