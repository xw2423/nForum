<?php
load('inc/pagination');

/**
 * class Friend is a set users that manage user's friends
 *
 * @implements Pageable
 * @author xw
 */
class Friend implements Pageable{

    private $_user = null;
    private $_num = null;

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

    /**
     * function getOnlineFriends get my online friends
     *
     * @return array the element is array(
     *    ["invisible"]=> bool(false)
     *    ["pid"]=> int(1)
     *    ["isfriend"]=> bool(true)
     *    ["idle"]=> int(0)
     *    ["userid"]=> string(6) "xw2423"
     *    ["username"]=> string(25) "<script>alert(1)</script>"
     *    ["userfrom"]=> string(14) "118.229.170.10"
     *    ["mode"]=> string(7) "Webä¯ÀÀ"
     * )
     * @access public
     */
    public static function getOnlineFriends(){
        $friends = array();
        $ret = bbs_getonlinefriends();
        if($ret == 0)
            return array();
        return $ret;
    }

    public function getTotalNum(){
        if(null === $this->_num)
            $this->_num = bbs_countfriends($this->_user->userid);
        return $this->_num;
    }

    /**
     * return array(array(ID,EXP)...)
     */
    public function getRecord($start, $num){
        $ret = bbs_getfriends($this->_user->userid, $start - 1, $num);
        if(!$ret)
            return array();
        return $ret;
    }
}
class FriendNullException extends Exception{}
class FriendAddException extends Exception{}
class FriendDeleteException extends Exception{}
