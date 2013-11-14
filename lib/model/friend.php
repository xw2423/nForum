<?php
load('inc/pagination');

/**
 * class Friend is a set users that manage user's friends
 *
 * @implements Pageable
 * @author xw
 */
class Friend implements Pageable{

    private static $_users = array();
    private $_user = null;
    private $_num = 0;

    /**
     * function __construct get a Friend object via $u
     * actually if $u is null it will get all the online users
     *
     * @param User $u
     * @return
     * @access public
     * @throws FriendNullException
     */
    public function __construct($u = null){
        if(!is_a($u, "User")){
            throw new FriendNullException();
        }
        $this->_user = $u;
        $this->_num = $u->getFriendNum();
    }

    public static function add($id){
        $ret = bbs_add_friend($id, "");
        switch($ret){
            case -1:
                throw new FriendAddException(ECode::$FRIEND_NOPRIV);
                break;
            case -2:
                throw new FriendAddException(ECode::$FRIEND_EXIST);
                break;
            case -3:
                throw new FriendAddException(ECode::$SYS_ERROR);
                break;
            case -4:
                throw new FriendAddException(ECode::$USER_NOID);
                break;
        }
        return true;
    }

    public static function delete($id){
        $ret = bbs_delete_friend($id);
        switch($ret){
            case 1:
                throw new FriendAddException(ECode::$USER_NOID);
                break;
            case 2:
                throw new FriendAddException(ECode::$FRIEND_NOEXIST);
                break;
            case 3:
                throw new FriendAddException(ECode::$SYS_ERROR);
                break;
        }
        return true;
    }

    public function getTotalNum(){
        return $this->_num;
    }

    public function getRecord($start, $num){
        $ret = $this->_user->getFriends($start - 1, $num);
        if(!$ret)
            return array();
        return $ret;
    }

    private function _isAll(){
        return is_null($this->_user);
    }
}
class FriendNullException extends Exception{}
class FriendAddException extends Exception{}
class FriendDeleteException extends Exception{}
