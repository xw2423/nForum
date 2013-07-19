<?php
/****************************************************
 * FileName: app/vendors/model/board.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
App::import("vendor", array("model/overload", "model/threads", "model/iwidget", "inc/pagination"));

/**
 * class Board is a board in kbs
 * Board is the element of collection,Board can be used to construct
 * Section only when board is directory
 * bid = -1 is favor dir
 * The base structure of $_info is
 *     array(12) {
 *        ["BID"]=> int(75)
 *        ["NAME"]=> string(5) "Flash"
 *        ["BM"]=> string(6) "xw2423"
 *        ["FLAG"]=> int(512)
 *        ["DESC"]=> string(8)
 *        ["CLASS"]=> string(6)
 *        ["SECNUM"]=> string(1) "6"
 *        ["LEVEL"]=> int(0)  //the right
 *        ["GROUP"]=> int(0)  //the parent dir id is it is in dir
 *        ["CURRENTUSERS"]=> int(1)
 *        ["LASTPOST"]=> int(43254)
 *        ["ARTCNT"]=> int(5)
 *         ["NPOS"]=> position of favor
 *         ["UNREAD"]=>
 *  }
 *
 * @extends OverloadObject
 * @implements Pageable
 * @implements iWidget
 * @author xw
 */
class Board extends OverloadObject implements Pageable, iWidget{

    /**
     * dir mode of board
     * @var string
     */
    public static $NORMAL = 0;
    public static $DIGEST = 1;
    public static $THREAD = 2;
    public static $MARK = 3;
    public static $DELETED = 4;
    public static $JUNK = 5;
    public static $ORIGIN = 6;

    /**
     * number of threads in board
     * @var int $threadsNum
     */
    public $_threadsNum = null;

    /**
     * board mode
     * @var int $_mode
     */
    private $_mode = 2;

    /**
     * function getInstance get a Board object from board name
     *
     * @param mixed $mixed int bid|string boardname
     * @return Board object
     * @static
     * @access public
     * @throws BoardNullException
     */
    public static function getInstance($mixed){
        $info = array();
        if(is_int($mixed) || preg_match("/^\d+$/", $mixed)){
            $ret = bbs_getboard_bid(intval($mixed), $info);
        }else{
            $ret = bbs_getboard_nforum($mixed, $info);
        }
        if($ret == 0)
            throw new BoardNullException();
        return new Board($info);
    }

    /**
     * function search match with board name
     *
     * @param $name
     * @return array
     * @static
     * @access public
     */
    public static function search($name){
        $boards = array();
        if (!bbs_searchboard(trim($name),0,$boards))
            return array();
        $ret = array();
        foreach($boards as $v){
            try{
                $ret[] = Board::getInstance($v['NAME']);
            }catch(BoardNullException $e){
            }
        }
        return $ret;
    }

    /**
     * function __contstruct()
     * do not use this to get a object
     *
     * @param array $info
     * @param int $pos
     * @return Board
     * @access public
     * @throws BoardNullException
     */
    public function __construct($info){
        if(!is_array($info))
            throw new BoardNullException();
        $this->_info = $info;
    }

    public function __get($name){
        switch($name){
            case 'BM':
                if($this->isDir())
                    return "[二级目录]";
                if($this->_info["$name"] === "" && !$this->isDir())
                    return "诚征版主中";
                break;
            case 'CLASS':
                if($this->isDir())
                    return "二级目录";
                break;
            case 'ARTCNT':
            case 'TOTAL':
            case 'CURRENTUSERS':
                if($this->isDir())
                    return 0;
                break;
        }
        return parent::__get($name);
    }

    public function getTotalNum(){
        if($this->_mode === self::$THREAD)
            return $this->getThreadsNum();
        else
            return $this->getTypeNum($this->_mode);
    }

    public function getRecord($start, $num){
        if($this->_mode === self::$THREAD)
            return $this->getThreads($start - 1, $num);
        else{
            return array_reverse($this->getTypeArticles($start - 1, $num, $this->_mode));
        }
    }

    public function wGetName(){
        return "board-" . $this->NAME;
    }

    public function wGetTitle(){
        return array("text"=>$this->DESC, "url"=>"/board/".$this->NAME);
    }

    public function wGetList(){
        App::import('Sanitize');
        $ret = array();
        $articles = array_reverse($this->getTypeArticles(0, 10, self::$ORIGIN));
        if(!empty($articles)){
            foreach($articles as $v){
                $ret[] = array("text"=>Sanitize::html($v->TITLE), "url"=>"/article/{$this->NAME}/{$v->GROUPID}");
            }
            return array("s"=>"w-list-line", "v"=>$ret);
        }else{
            return array("s"=>"w-list-line", "v"=>array(array("text" => ECode::msg(ECode::$BOARD_NOTHREADS), "url" => "")));
        }
    }

    public function wGetTime(){
        $file = 'boards/' . $this->NAME . '/.ORIGIN';
        if(!file_exists($file))
            return time();
        return filemtime($file);
    }

    public function wHasPerm($u){
        return $this->hasReadPerm($u);
    }

    /**
     * function getThreads get a range of threads
     * it will contain the top threads, the sequence will change when there is a new post
     * index start in zero
     * this threads only has first and last article
     *
     * @param int $start
     * @param int $num
     * @return array
     * @access public
     */
    public function getThreads($start,$num){
        if($this->getThreadsNum() == 0)
            return array();
        $arr = bbs_getthreads($this->NAME, $start, $num, 1);
        if(!is_array($arr))
            return array();
        foreach($arr as &$v){
            $v = new Threads($v, $this);
        }
        return $arr;
    }

    /**
     * function getThreadsNum get number of threads which is only for Threads mode
     *
     * @return int
     */
    public function getThreadsNum(){
        if(null === $this->_threadsNum){
            $this->_threadsNum = bbs_getthreadnum($this->BID);
            if($this->_threadsNum < 0)
                 $this->_threadsNum = 0;
        }
        return $this->_threadsNum;
    }

    /**
     * function getLastThreads get the last threads of board not top article
     *
     * @return Threads
     * @access public
     */
    public function getLastThreads(){
        $threads = $this->getThreads(0, 15);
        if(!is_array($threads))
            return null;
        foreach($threads as $v){
            if(!$v->FIRST->isTop()){
                return $v;
            }
        }
        return null;
    }

    /**
     * function getTypeArticles get a range of articles via $type
     * $NORMAL get articles like in telnet
     * $DIGEST digest articles
     * $DELETED delete articles
     * $JUNK junk articles
     * $ORIGIN same threads mode
     * $ZHIDING top articles
     *
     * @param int $start start with zero
     * @param int $num
     * @param int $type
     * @return array
     * @access public
     */
    public function getTypeArticles($start, $num, $type = null){
        if(is_null($type))
            $type = self::$NORMAL;
        $totalNum = $this->getTypeNum($type);
        $start = $totalNum - $num - $start + 1;
        $ret = bbs_getarticles($this->NAME, $start, $num, $type);
        if(!is_array($ret))
            return array();
        foreach($ret as $k => &$v){
            $v = new Article($v, $this, $k + $start);
        }
        return $ret;
    }

    /**
     * function getTypeNum get the article number of $type
     *
     * @param int $type
     * @return int
     * @access public
     */
    public function getTypeNum($type = null){
        if(is_null($type))
            $type = self::$NORMAL;
        return bbs_countarticles($this->BID, $type);
    }

    /**
     * function isSortMode show the mode(default current mode) whether article is accessed by position
     * if return false ,the articles in this mode can only be accessed via its position
     *
     * @param int mode
     * @return boolean
     * @access public
     */
    public function isSortMode($mode = null){
        if(null === $mode)
            $mode = $this->_mode;
        return ($mode == BOARD::$THREAD || $mode == BOARD::$NORMAL || $mode == BOARD::$ORIGIN);
    }

    /**
     * function isValidMode show ths mode(default current mode) can be accessed or not
     *
     * @param int mode
     * @return boolean
     * @access public
     */
    public function isValidMode($mode = null){
        if(null === $mode)
            $mode = $this->_mode;
        $o = new ReflectionClass('Board');
        return in_array($mode, $o->getStaticProperties());
    }

    /**
     * function setMode change current board mode
     *
     * @param int $mode
     * @return boolean
     * @access public
     */
    public function setMode($mode){
        $mode = intval($mode);
        if($this->isValidMode($mode)){
            $this->_mode = $mode;
            return true;
        }
        return false;
    }

    /**
     * function getMode get current board mode
     *
     * @return int
     * @access public
     */
    public function getMode(){
        return $this->_mode;
    }

    /**
     * function hasReadPerm whether board can read
     * it also check the current mode can be read
     *
     * @param User $user
     * @return boolean true|false
     * @access public
     */
    public function hasReadPerm($user){
        if($this->_mode === Board::$DELETED && !$user->isBM($this) && !$user->isAdmin())
            return false;
        if($this->_mode === Board::$JUNK && !$user->isAdmin())
            return false;

        if(bbs_checkreadperm($user->uid, $this->BID) == 0)
            return false;
        return true;
    }

    /**
     * function hasPostPerm whether board can post
     *
     * @param User $user
     * @return boolean true|false
     * @access public
     */
    public function hasPostPerm($user){
        if(bbs_checkpostperm($user->uid, $this->BID) == 0)
            return false;
        return true;
    }

    /**
     * function isDeny check whether board deny user
     *
     * @param User $user
     * @return boolean true|false
     * @access public
     */
    public function isDeny($user){
        return (bbs_deny_me($user->userid, $this->NAME) != 0);
    }

    /**
     * function getTodayNum get the number that post today
     *
     * @return int
     * @access public
     */
    public function getTodayNum(){
        $num = bbs_get_today_article_num($this->NAME);
        return ($num >= 0)?$num : 0;
    }

    /**
     * function getElitePath
     *
     * @return string
     * @access public
     */
    public function getElitePath(){
        $ret = bbs_getannpath($this->NAME);
        if($ret === false)
            return "";
        $ret = preg_replace("/^0Announce\//", "", $ret);
        return $ret;
    }

    /**
     * function getVotes get vote list of board
     * array(
     *     'USERID' => string
     *     'TITLE' => string
     *     'DATE' => int
     *     'TYPE' => string '是非' (length=4)
     *     'MAXDAY' => int
     * )
     *
     * @return array
     * array(
     *     'owner' => string
     *     'title' => string
     *     'start' => int
     *     'type' => string '是非' (length=4)
     *     'day' => int
     * )
     * @access public
     */
    public function getVotes(){
        $arr = array();
        $num = bbs_get_votes($this->NAME, $arr);
        if($num <= 0)
            return array();
        return $arr;
    }

    /**
     * function getVote get vote of board via num
     * array(
     *     'USERID' => string
     *     'TITLE' => string
     *     'DATE' => int
     *     'TYPE' => string '是非' (length=4)
     *     'MAXDAY' => int
     *     'MAXTKT' => int 1
     *     'DESC' => int
     *     'TOTALITEMS' => int
     *     'ITEM1' => string
     *     'ITEM2' => string
     *     'ITEM3' => string
     *     'ITEM4' => string
     *     'ITEM5' => string
     *     'ITEM6' => string
     *     'ITEM7' => string
     *     'VOTED1' => int 1
     *     'VOTED2' => int 1
     *     'MSG1' => string
     *     'MSG2' => string
     *     'MSG3' => string
     * )
     *
     * @param int $num
     * @return mixed array|false
     * array(
     *     'owner' string
     *     'title' string
     *     'start' int
     *     'type' string
     *     'day' int
     *     'limit' int
     *     'desc' string
     *     'val' array
     * )
     * @access public
     */
    public function getVote($num){
        $arr = array();
        $res = array();
        $num = bbs_get_vote_from_num($this->NAME,$arr,$num,$res);
        if($num < 0)
            return false;
        $ret['owner'] = $arr[0]['USERID'];
        $ret['title'] = $arr[0]['TITLE'];
        $ret['start'] = $arr[0]['DATE'];
        $ret['type'] = $arr[0]['TYPE'];
        $ret['day'] = $arr[0]['MAXDAY'];
        $ret['limit'] = $arr[0]['MAXTKT'];
        $ret['desc'] = @bbs_printansifile("vote/" . $this->NAME . "/desc." . $arr[0]["DATE"]);
        $voted = isset($res[0]['VOTED1']);
        switch($arr[0]['TYPE']){
            case '数字':
                $ret['val'] = $voted?$res[0]['VOTED1']:"";
                break;
            case '问答':
                $ret['val'] = false;
                break;
            default:
                foreach(range(1, $arr[0]['TOTALITEMS']) as $i){
                    $ret['val'][] = array($arr[0]['ITEM'.$i], $voted && ($res[0]['VOTED'.($i <= 32?'1':'2')] & (1 << (($i - 1) % 32))) != 0);
                }
        }
        $ret['msg'] = $voted?trim(join("\n", array($res[0]['MSG1'], $res[0]['MSG2'], $res[0]['MSG3']))):"";
        return $ret;
    }

    /**
     * function vote
     *
     * @param int $num
     * @param array $val1
     * @param array $val2
     * @param string msg
     * @return boolean
     * @access public
     */
    public function vote($num, $val1, $val2, $msg){
        $msg = trim(preg_replace("|^((.*?\n){3})[\s\S]*$|", "\$1", $msg));
        $ret = bbs_vote_num($this->NAME, $num, intval($val1), intval($val2), $msg);
        return ($ret > 0);
    }

    /**
     * function setOnBoard set current user on this board
     *
     * @return null
     * @access public
     */
    public function setOnBoard(){
        bbs_set_onboard($this->BID, 1);
    }

    public function isReadOnly(){
        return $this->_checkFlag(BBS_BOARD_READONLY);
    }

    public function isAttach(){
        return $this->_checkFlag(BBS_BOARD_ATTACH);
    }

    public function isNoReply(){
        return $this->_checkFlag(BBS_BOARD_NOREPLY);
    }

    public function isAnony(){
        return $this->_checkFlag(BBS_BOARD_ANNONY);
    }

    public function isOutgo(){
        return $this->_checkFlag(BBS_BOARD_OUTFLAG);
    }

    public function isTmplPost(){
        return $this->_checkFlag(BBS_BOARD_TMP_POST);
    }

    //true means: no club && (no perm || has PERM_POSTMASK|PERM_DEFAULT)
    //see libBBS/boards.c
    public function isNormal(){
        return (bbs_normalboard($this->NAME) == 1);
    }

    /**
     * function isDir check whether is directory board
     *
     * @return boolean
     * @access public
     */
    public function isDir(){
        //normal dir board || fav dir
        return $this->_checkFlag(BBS_BOARD_GROUP) || $this->BID == -1;
    }

    /**
     * function getDir get the parent board
     *
     * @return Board
     * @access public
     */
    public function getDir(){
        try{
            if($this->GROUP != 0)
                return self::getInstance($this->GROUP);
            return null;
        }catch(BoardNullException $e){
            return null;
        }

    }

    public function getTitleKey($sys = true){
        $tmp = array();
        $ret = bbs_gettitkey($this->NAME, $tmp, $sys?1:0);
        if(false === $ret)
            return false;
        foreach($tmp as &$v)
            $v = $v['desc'];
        return $tmp;
    }

    public function addDeny($userid = '', $reason, $day, $article = 0){
        $ret = bbs_denyadd($this->NAME, $userid, $reason, $day, $article, 0);
        $code = null;
        switch ($ret) {
            case -1:
                $code = ECode::$BOARD_NONE;
                break;
            case -2:
                $code = ECode::$ARTICLE_NOMANAGE;
                break;
            case -3:
                $code = ECode::$USER_NOID;
                break;
            case -4:
                $code = ECode::$DENY_DENIED;
                break;
            case -5:
                $code = ECode::$DENY_INVALIDDAY;
                break;
            case -6:
                $code = ECode::$DENY_NOREASON;
                break;
            case -7:
                $code = ECode::$DENY_CANTPOST;
                break;
            default:
                break;
        }
        if (!is_null($code))
            throw new BoardDenyException($code);
    }

    public function modDeny($userid, $reason, $day){
        $ret = bbs_denymod($this->NAME, $userid, $reason, $day, 0);
        $code = null;
        switch ($ret) {
            case -1:
                $code = ECode::$BOARD_NONE;
                break;
            case -2:
                $code = ECode::$ARTICLE_NOMANAGE;
                break;
            case -3:
                $code = ECode::$USER_NOID;
                break;
            case -4:
                $code = ECode::$DENY_NOTDENIED;
                break;
            case -5:
                $code = ECode::$DENY_INVALIDDAY;
                break;
            case -6:
                $code = ECode::$DENY_NOREASON;
                break;
            case -7:
                $code = ECode::$DENY_CANTPOST;
                break;
            default:
                break;
        }
        if (!is_null($code))
            throw new BoardDenyException($code);
    }

    public function delDeny($userid){
        $ret = bbs_denydel($this->NAME, $userid);
        $code = null;
        switch ($ret) {
            case -1:
                $code = ECode::$BOARD_NONE;
                break;
            case -2:
                $code = ECode::$ARTICLE_NOMANAGE;
                break;
            case -3:
                $code = ECode::$DENY_NOTDENIED;
                break;
        }
        if (!is_null($code))
            throw new BoardDenyException($code);
    }

    // $all == false indicates custom reasons only
    public function getDenyReasons($all = true){
        $ret = array();
        bbs_getdenyreason($this->NAME, $ret, $all);
        return $ret;
    }

    public function getDeny() {
        $data = array();
        $ret = bbs_denyusers($this->NAME, $data);
        $code = null;
        switch ($ret) {
            case -1:
                $code = ECode::$SYS_ERROR;
                break;
            case -2:
                $code = ECode::$BOARD_NONE;
                break;
            case -3:
                $code = ECode::$ARTICLE_NOMANAGE;
                break;
            default:
                break;
        }
        if (!is_null($code))
            throw new BoardDenyException($code);
        return $data;
    }

    private function _checkFlag($flag){
        return ($this->FLAG & $flag)?true:false;
    }
}
class BoardNullException extends Exception {}
class BoardDenyException extends Exception {}
?>
