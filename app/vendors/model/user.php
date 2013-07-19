<?php
/****************************************************
 * FileName: app/vendors/model/user.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
/**
 * class User
 * the function that phplib provide is !@#$%^&*, so the info structure will diffrent while using diffrent method
 * The base structure of User is
 * array(52) {
 *         ["uid"]=> int(3001) //position in .PASSWORD
 *         ["userid"]=> string(5) "guest"
 *         ["firstlogin"]=> int(1100445298)
 *         ["exittime"]=> int(1248253332)
 *         ["lasthost"]=> string(12) "211.68.71.41"
 *         ["numlogins"]=> int(1132) //登陆次数
 *         ["numposts"]=> int(0)
 *         ["flag1"]=> int(9)
 *         ["title"]=> int(0)
 *         ["username"]=> string(5) "guest"
 *         ["md5passwd"]=> string(16) ""
 *         ["realemail"]=> string(0) ""
 *         ["userlevel"]=> int(536879104)
 *         ["lastlogin"]=> int(1249022581)
 *         ["stay"]=> int(1266631879)
 *         ["realname"]=> string(0) ""
 *         ["address"]=> string(0) ""
 *         ["email"]=> string(0) ""
 *         ["signature"]=> int(0)
 *         ["signum"]=> int(0)
 *         ["userdefine0"]=> int(3221224447)
 *         ["userdefine1"]=> int(4294967295)
 *         ["mailbox_prop"]=> int(1)
 *         ["gender"]=> int(0)
 *         ["birthyear"]=> int(0)
 *         ["birthmonth"]=> int(0)
 *         ["birthday"]=> int(0)
 *         ["reg_email"]=> string(0) ""
 *         ["mobilderegistered"]=> int(0)
 *         ["mobilenumber"]=> string(0) ""
 *         ["OICQ"]=> string(0) ""
 *         ["ICQ"]=> string(0) ""
 *         ["MSN"]=> string(0) ""
 *         ["homepage"]=> string(0) ""
 *         ["userface_img"]=> int(0)
 *         ["userface_url"]=> string(0) ""
 *         ["userface_width"]=> int(0)
 *         ["userface_height"]=> int(0)
 *         ["group"]=> int(0)
 *         ["country"]=> string(0) ""
 *         ["province"]=> string(0) ""
 *         ["city"]=> string(0) ""
 *         ["shengxiao"]=> int(0)
 *         ["bloodtype"]=> int(0)
 *         ["religion"]=> int(0)
 *         ["profession"]=> int(0)
 *         ["married"]=> int(0)
 *         ["education"]=> int(0)
 *         ["graduateschool"]=> string(0) ""
 *         ["character"]=> int(0)
 *         ["photo_url"]=> string(0) ""
 *         ["telephone"]=> string(0) ""
 * if the user is current user, this is user info
 *         ["index"]=> int(1) position in utmpshm
 *         ["active"]=> int(1)
 *         ["uid"]=> int(3001)
 *         ["pid"]=> int(1)
 *         ["invisible"]=> int(1)
 *         ["sockactive"]=> int(0)
 *         ["sockaddr"]=> int(0)
 *         ["destuid"]=> int(14912)
 *         ["mode"]=> int(15)
 *         ["pager"]=> int(0)
 *         ["in_chat"]=> int(0)
 *         ["chatid"]=> string(0) ""
 *         ["from"]=> string(12) "211.68.71.41"
 *         ["logintime"]=> int(1249039809)
 *         ["freshtime"]=> int(1249039829)
 *         ["utmpkey"]=> int(2995880)
 *         ["realname"]=> string(0) ""
 * end if
 * if the user is get from getOnlineFriend or online user
 *         ["invisible"]=> bool(false)
 *         ["pid"]=> int(1)
 *         ["isfriend"]=> bool(true)
 *         ["idle"]=> int(0)
 *         ["userid"]=> string(6) "xw2423"
 *         ["username"]=> string(25) "<script>alert(1)</script>"
 *         ["userfrom"]=> string(14) "118.229.170.10"
 *         ["mode"]=> string(7) "Web浏览"
 * end if
 * if the user is friend
 *         ["exp"]=> string(7)
 * end if
 * }
 * @see
 * @see bbs_getcurrentuser in phpbbs_user.c
 * @see bbs_getcurrentuinfo in phpbbs_user.c
 * @see bbs_getuser in phpbbs_user.c
 * @see bbs_getonlinefriends in phpbbs_friend.c
 * @see bbs_getfriends in phpbbs_friend.c
 * @author xw
 */
App::import("vendor", array("model/overload" ,"model/code"));
class User extends OverloadObject{

    /**
     * actually i divide user level only 6 types
     * reg is register ok
     * bm is board manager
     * boards is the one who can edit article
     * admin is the one who has S
     * 3 -- oh, it is delicious!
     * what is the sixth? i think that you aren't
     * you can use hasPerm() to valid them but phpbbslib may not have enough constant
     */
    public static $USER_REG = BBS_PERM_LOGINOK;
    public static $USER_BM = BBS_PERM_BOARDS;
    public static $USER_BOARDS = BBS_PERM_OBOARDS;
    public static $USER_ADMIN = BBS_PERM_SYSOP;
    public static $USER_3 = BBS_PERM_ADMIN;

    private static $_instance = null;

    private static $_users = array();

    private $_customList = array();

    private $_friendNum = null;

    /**
     * function getInstance get a User object via userid
     *
     * @param string $id
     * @return User object
     * @static
     * @access public
     * @throws UserNullException
     */
    public static function getInstance($id = null, $cache = true){
        $info = $uinfo = array();
        if(is_null($id)){
            if(is_null(self::$_instance)){
                //the call sequence can not be exchange
                if(bbs_getcurrentuinfo($uinfo) == 0){
                    throw new UserNullException();
                }
                bbs_getcurrentuser($info);
                self::$_instance = new User($info, $uinfo);
            }
            return self::$_instance;
        }
        if(in_array($id, array_keys(self::$_users)))
            return self::$_users[$id];
        if(bbs_getuser($id, $info) == 0){
            throw new UserNullException();
        }
        $u = new User($info, $uinfo);
        if($cache) self::$_users[$id] = $u;
        return $u;
    }

    /**
     * function update return a new User object of current user
     *
     * @return User object
     * @static
     * @access public
     */
    public static function update(){
        self::$_instance = null;
        return User::getInstance();
    }

    /**
     * function create create a new user
     *
     * @param string $id
     * @param string $pwd
     * @param string $name nickname
     * @return null
     * @static
     * @access public
     * @throws UserCreateException
     */
    public static function create($id, $pwd, $name){
        $ret = bbs_createnewid($id, $pwd, $name);
        switch($ret){
            case 0:
                break;
            case 1: case 2: case 5: case 6:
                throw new UserCreateException(ECode::$REG_FORMAT);
            case 3: case 4:
                throw new UserCreateException(ECode::$REG_IDUSED);
            case 10:
                throw new UserCreateException(ECode::$SYS_ERROR);
            default:
                throw new UserCreateException(ECode::$SYS_ERROR);
        }
    }

    /**
     * function getPerm get userlevel in $pos
      *
     * @param int $pos the bit postion
     * @return mixed 1|0
     * @access public
     */
    public function getPerm($pos){
        return ($this->userlevel & (1<<$pos))?1:0;
    }

    /**
     * function setPerm set userlevel via $val base on current
     * the structure of $val is
     * array("pos"=>int, "val"=>1|0)
     *
     * @param array $val
     * @return void
     * @access public
     */
    public function setPerm($val){
        $prop = $this->userlevel;
        foreach($val as $v){
            if($v['val'] == 1)
                $prop |= (1 << $v['pos']);
            else
                $prop &= ~(1 << $v['pos']);
        }
        bbs_admin_setuserperm($this->userid, $prop);
    }

    /**
     * function hasPerm check current userlevel
     *
     * @param string $perm the static variables define in User
     * @return boolean true|false
     */
    public function hasPerm($perm){
        return ($this->userlevel & $perm)?true:false;
    }

    public function isReg(){
        return $this->hasPerm(self::$USER_REG);
    }

    /**
     * function isBM check current user is a board manager of $board
     * if $board is null check the userlevel $USER_BM
     *
     * @param Board $board
     * @return boolean true|false
     * @access public
     */
    public function isBM($board = null){
        if(is_a($board, "Board")){
            if(stripos($board->BM, $this->userid) !== false)
                return true;
            return false;
        }
        return $this->hasPerm(self::$USER_BM);
    }

    public function isAdmin(){
        return $this->hasPerm(self::$USER_BOARDS | self::$USER_ADMIN | self::$USER_3);
    }

    public function is3(){
        return $this->hasPerm(self::$USER_3);
    }

    public function isOnline(){
        return bbs_isonline($this->userid);
    }

    /**
     * function getStatus get all status string of current user
     * current user will have many clients
     *
     * @return $string
     * @access public
     */
    public function getStatus(){
        $mode = bbs_getusermode($this->userid);
        if($mode !== 0)
            return substr($mode, 1);
        return "目前不在站上";
    }

    /**
     * function getLevel get the string format of current user's level
     *
     * @return $string
     * @access public
     */
    public function getLevel(){
        return bbs_getuserlevel($this->userid);
    }

    public function getLife(){
        return bbs_compute_user_value($this->userid);
    }

    public function getSignature(){
        $sigFile = $this->getHome("signatures");
        $fp = @fopen($sigFile, "r");
        $sig =  "";
        if($fp){
            while(!feof($fp)){
                $sig .= fgets($fp, 300);
            }
        }
        return $sig;
    }

    public function setSignature($signature){
        App::import("vendor", "inc/ubb");
        $fileName = $this->getHome("signatures");
        $fp = @fopen($fileName,"w+");
        if ($fp != false) {
            fwrite($fp, str_replace("\r\n", "\n", XUBB::remove($signature)));
            fclose($fp);
            bbs_recalc_sig();
        }
    }

    public function setInfo($gender, $year, $month, $day, $email, $qq, $msn, $homepage, $uface, $furl, $fwidth, $fheight, $tname = null, $address = null, $phone = null){
        $tname = empty($tname)?$this->realname:$tname;
        $address = empty($address)?$this->address:$address;
        $phone = empty($phone)?$this->telephone:$phone;
        $ret = bbs_saveuserdata(
            $this->userid,
            $tname,//真实姓名
            $address,//地址
            $gender,$year, $month,$day,$email,
            $phone,//电话
            $phone,//手机
            $qq,
            "",//icq
            $msn,  $homepage, intval($uface), $furl,
            intval($fwidth), intval($fheight),
            0,//门派
            "", //国家
            "", //省份
            "", //城市
            0, //生肖
            0, //血型
            0, //信仰
            0, //职业
            0, //婚姻
            0, //教育
            "", //大学
            0, //性格
            0, //个人照片
            false //非注册？
        );
        switch($ret){
            case 0:
                break;
            case -1:
                throw new UserSaveException(ECode::$USER_EWIDTH);
                break;
            case -2:
                throw new UserSaveException(ECode::$USER_EHEIGHT);
                break;
            case 3:
                throw new UserSaveException(ECode::$USER_NOID);
                break;
            default:
                throw new UserSaveException(ECode::$SYS_ERROR);
        }
    }

    public function reg($tname, $dept, $address, $gender, $year, $month, $day, $email, $phone, $mobile, $auto){
        $ret = bbs_createregform($this->userid ,$tname,$dept,$address,$gender,$year,$month,$day,$email,$phone,$mobile,false);
        switch($ret){
            case 0:
                break;
            case 1:
                throw new UserRegException(ECode::$REG_HAVAFORM);
                break;
            case 4:
                throw new UserRegException(ECode::$REG_REGED);
                break;
            default:
                throw new UserRegException(ECode::$SYS_ERROR);
        }
    }

    /**
     * function getCustom get the custom property of user
     * the property keys is in $this->_customList
      *
     * @param string $field
     * @param int $pos the bit postion
     * @return mixed 1|0|null
     * @access public
     */
    public function getCustom($field, $pos){
        if(!in_array($field, $this->_customList))
            return null;

        return ($this->_info[$field] & (1<<$pos))?1:0;
    }

    /**
     * function setCustom set user custom via $val
     * the structure of $val is
     * array($k => array("pos"=>int, "val"=>))
     * the $k must in $this->_customList
     *
     * @param array $val
     * @return void
     * @access public
     */
    public function setCustom($val){
        foreach($this->_customList as $p){
            if(!isset($val[$p]))
                return false;
            $prop = $this->{$p};
            foreach($val[$p] as $v){
                if($v['val'] == 1)
                    $prop |= (1 << $v['pos']);
                else
                    $prop &= ~(1 << $v['pos']);
            }
            $$p = $prop;
        }
        bbs_setuserparam(${$this->_customList[0]},${$this->_customList[1]},${$this->_customList[2]});
    }

    public function getFriendNum(){
        if(is_null($this->_friendNum))
            $this->_friendNum = bbs_countfriends($this->userid);
        return $this->_friendNum;
    }

    /**
     * function getFriends get my friends
     * bbs_getfriends return array(ID,EXP)
     *
     * @param int $start
     * @param int $num
     * @return array the element is User
     * @access public
     */
    public function getFriends($start = 0, $num = null){
        $friends = array();
        $num = is_null($num)?$this->getFriendNum():$num;
        $res = bbs_getfriends($this->userid, $start, $num);
        foreach((array)$res as $v){
            $info = array();
            if(bbs_getuser($v['ID'], $info) != 0)
                $friends[] = new User($info,array("exp"=>$v['EXP']));
        }
        return $friends;
    }

    /**
     * function getOnlineFriends get my online friends
     * online friends has more info
     *
     * @return array the element is User
     * @access public
     */
    public function getOnlineFriends(){
        $friends = array();
        $ret = bbs_getonlinefriends();
        if($ret == 0)
            return array();
        foreach($ret as $v){
            $info = array();
            if(bbs_getuser($v['userid'], $info) == 0){
                throw new UserNullException();
            }
            $friends[] = new User($info, $v);
        }
        return $friends;
    }

    /**
     * function setPwd set current password
     * be carefull this action will not log
     *
     * @param string $pwd
     * @return boolean true|false
     * @access public
     */
    public function setPwd($pwd){
        return (bbs_setuserpasswd($this->userid, $pwd) == 0);
    }

    /**
     * function getHome get the path of current user in file system
     *
     * @param string $file
     * @return string
     * @access public
     */
    public function getHome($file = null){
        if(is_null($file))
            return bbs_sethomefile($this->userid);
        return bbs_sethomefile($this->userid, $file);
    }

    /**
     * function getFace get a avaliable image url of current user
     * if no custom face use img/face_default_?.jpg
     *
     * @return string
     * @access public
     */
    public function getFace(){
        if($this->userface_url != "" && strpos($this->userface_url, Configure::read("user.face.dir"). "/") === 0){
            $furl = "/" . $this->userface_url;
        }else{
            $furl = "/img/face_default_" . (($this->gender == "77")?"m":"f") . ".jpg";
        }

        return $furl;
    }

    /**
     * function setName set user nick name
     *
     * @param string $name
     * @param boolean $tmp true:temp modify|false
     * @return boolean true:success|false:fail
     * @access public
     */
    public function setName($name, $tmp = false){
        $tmp = $tmp?1:0;
        return (bbs_modify_nick($name, $tmp) == 0);
    }

    /**
     * function __construct get a User object via $info(main info) & $uinfo(other info)
     * the $uinfo is user login info, it will rewrite the value in $info
     * do not use it to get a object unless you know this constructor
     *
     * @param array $info
     * @param array $uinfo
     * @return User object
     * @access public
     */
    public function __construct($info, $uinfo) {
        if(!is_array($info) || !is_array($uinfo))
            throw new UserNullException();
        $this->_info = array_merge($info, $uinfo);
        $this->_customList = array_keys(Configure::read("user.custom"));
        unset($info);unset($uinfo);
    }
}
class UserNullException extends Exception {}
class UserSaveException extends Exception {}
class UserCreateException extends Exception {}
class UserRegException extends Exception {}
?>
