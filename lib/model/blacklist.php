<?php
load('inc/pagination');

/**
 * class Blacklist is a set users that manage user's blacklist
 *
 * @implements Pageable
 * @author xw
 */
class Blacklist implements Pageable{

    private $_user = null;
    private $_num = null;

    public function __construct($u = null){
        if(!is_a($u, "User")){
            throw new BlacklistNullException();
        }
        $this->_user = $u;
    }

    public static function add($id){
        $ret = bbs_add_ignore($id);
        switch($ret){
            case -1:
                throw new BlacklistAddException(ECode::$BLACKLIST_MAX);
                break;
            case -2:
                throw new BlacklistAddException(ECode::$BLACKLIST_ERROR);
                break;
            case -3:
                throw new BlacklistAddException(ECode::$SYS_ERROR);
                break;
            case -4:
                throw new BlacklistAddException(ECode::$USER_NOID);
                break;
        }
        return true;
    }

    public static function delete($id){
        $ret = bbs_delete_ignore($id);
        switch($ret){
            case 2:
                throw new BlacklistDeleteException(ECode::$BLACKLIST_NOEXIST);
                break;
            case 3:
                throw new BlacklistDeleteException(ECode::$SYS_ERROR);
                break;
        }
        return true;
    }

    public function getTotalNum(){
        if(null === $this->_num)
            $this->_num = (int)bbs_countignores($this->_user->userid);
        return $this->_num;
    }

    /**
     * return array(array(ID)...)
     */
    public function getRecord($start, $num){
        if($this->getTotalNum() === 0)
            return array();
        $ret = bbs_getignores($this->_user->userid, $start - 1 ,$num);
        if(!$ret)
            return array();
        return $ret;
    }
}
class BlacklistNullException extends Exception{}
class BlacklistAddException extends Exception{}
class BlacklistDeleteException extends Exception{}
