<?php
/****************************************************
 * FileName: app/vendors/model/threads.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
App::import("vendor", array("model/article", "inc/pagination"));

/**
 * class Threads is a Article but contains other article which groupid equal its id
 * other than the properties in Article, Threads has three new properties
 * $this->FIRST is the first Article of Threads
 * $this->LAST is the last Article of Threads
 * $this->articleNum is number of articles in Threads
 * 
 * if the first article is deleted, it will be the next article,
 * but its ID  will be not threads id, and its GROUPID also will be wrong 
 * when get from function getInstance(what a fuck)
 * using the article's GROUPID to find threads ID
 *
 * @extends Article
 * @implements Pageable       
 * @author xw
 */
class Threads extends Article implements Pageable{

    /**
     * number of articles 
     * @var int $articleNum
     */
    public $articleNum;

    /**
     * the reference of last article
     * @var Article $_last
     */
    private $_last;

    /**
     * array of all the articles
     * @var array $_articles
     */
    private $_articles = array();

    /**
     * function getInstance get a Threads object via $gid & $board
     * suggest using this method to get a ref of Threads
     *
     * @param int $gid
     * @param Board $board
     * @return Threads object
     * @static
     * @access public
     * @throws ThreadsNullException
     */
    public static function getInstance($gid, $board){
        $arr = array();
        $haveprev = null;
        $gid = intval($gid);
        $ret = bbs_get_threads_from_gid($board->BID, $gid, $gid, $arr, $haveprev, 1);
        if($ret == 0 || count($arr) == 0)
            throw new ThreadsNullException();
        $num = count($arr);
        return new Threads(array($arr[0], $arr[$num - 1], $num), $board, $arr);
    }

    /**
     * function search
     *
     * @param Board $board
     * @param string $t1
     * @param string $t2
     * @param string $tn not contain
     * @param string $author
     * @param int $day
     * @param boolean $m
     * @param boolean $a attachment
     * @param int $return result number
     * @return array
     * @access public
     */
    public static function search($board, $t1, $t2, $tn, $author, $day, $m, $a, $return){
        $m = ($m)?1:0;
        $a = ($a)?1:0;
        $ret = bbs_searchtitle($board->NAME, $t1, $t2, $tn, $author, intval($day), $m, $a, intval($return));
        if(!is_array($ret))
            return array();
        foreach($ret as &$v){
            $v = new Threads($v, $board);
        }
        return $ret;
    }

    /**
     * function __contstruct()
     * The structure of $info is
     * array(2) {
     *         [0|origin]=> first Article 
     *         [1|lastreply]=> last Article 
     *         [2|articlenum]=> num
     * }
     * do not use this to get a object
     *
     * @param array $info
     * @param Board $board
     * @param array $articles
     * @return Threads
     * @access public
     * @throws ThreadsNullException
     */
    public function __construct($info, $board, $articles = null) {
        if(!array($info) || count($info) != 3)
            throw new ThreadsNullException();
        if(!is_a($board, "Board"))
            throw new ThreadsNullException();

        //make sure it is number-index
        $info = array_values($info);
        $this->articleNum = intval($info[2]);
        $this->_board = $board;
        $this->_last = new Article($info[1], $this->_board, $this->articleNum - 1);
        try{
            parent::__construct($info[0], $this->_board, 0);
        }catch(ArticleNullException $e){
            throw new ThreadsNullException();
        }
        if(is_array($articles)){
            foreach($articles as $k=>$v){
                try{
                    $this->_articles[] = new Article($v, $this->_board, $k);    
                }catch(Exception $e){
                    throw new ThreadsNullException();
                }
            }
        }
    }

    public function __get($name){
        switch($name){
            case "FIRST":
                return $this;
            case "LAST":
                return $this->_last;
            default:
                return parent::__get($name);
        }
    }

    public function getTotalNum(){
        return $this->articleNum;
    }

    public function getRecord($start, $num){
        return array_slice($this->_articles, $start - 1, $num);
    }

    public function getArticle($pos) {
        if(isset($this->_articles[$pos]))
            return $this->_articles[$pos];
        return null;
    }

    public function getArticleById($id){
        foreach($this->_articles as $v){
            if($v->ID == $id)
                return $v;
        }
        return null;
    }

    /**
     * function forward mail this thread to sb.
     *
     * @param string $target
     * @param int $start
     * @param boolean $ref
     * @param boolean $noatt
     * @param boolean $noansi
     * @param boolean $big5
     * @return null
     * @access public
     * @throws ArticleForwardException
     */
    public function forward($target, $start = 0, $noref = false, $noatt = false , $noansi = false, $big5 = false){
        $code = null;
        if($start == 0)
            $start = $this->GROUPID;
        $ret = bbs_dotforward($this->_board->NAME, $this->GROUPID, $start, $target, $big5, $noansi, $noref, $noatt);
        switch ($ret) {
            case -1:
            case -10:
            case -7:
                $code = ECode::$SYS_ERROR;
                break;
            case -8:
                $code = ECode::$USER_NOID;
                break;
            case -11:
                $code = ECode::$BOARD_NONE;
                break;
        }
        if(!is_null($code))
            throw new ThreadsForwardException($code);
    }

    /**
     * function delete remove the threads
     *
     * @access public
     * @override
     */
    public function delete(){
        $this->deleteArticles(0, $this->articleNum);
    }
    /**
     * function deletedArticles delete the articles in threads
     * no exception 
     *
     * @param int $pos
     * @param int $num
     * @access public
     */
    public function deleteArticles($pos, $num){
        $i = 0;
        while($i <= $num){
            $a = $this->getArticle($pos + $i);
            if(!is_null($a))
                $a->delete();
            $i ++;
        }
    }

    /**
     * function manage set threads flag
     *
     * @param int $op
     *     0 - nothing
     *     1 - delete
     *     2 - mark
     *     3 - unmark
     *     4 - del X records
     *     5 - put to announce
     *     6 - set X flag
     *     7 - unset X flag
     *     8 - no reply
     *     9 - cancel no reply
     */
    public function manage($op, $start = null){
        $s = $this->FIRST->ID;
        if(null !== $start){
            foreach($this->_articles as $v){
                if($start == $v->ID){
                    $s = $start;
                    break;
                }
            }
        }
        $code = null;
        $ret = bbs_threads_bmfunc($this->_board->BID, $this->GROUPID, $s, $op);
        switch ($ret) {
            case -1:
                $code = ECode::$BOARD_NONE;
                break;
            case -2:
                $code = ECode::$ARTICLE_NOMANAGE;
                break;
            case -3:
            case -10:
                $code = ECode::$SYS_ERROR;
                break;
            default:
                break;
        }
        if(!is_null($code))
            throw new ThreadsManageException($code);
        return $ret;
    }

}
class ThreadsNullException extends Exception {}
class ThreadsForwardException extends Exception {}
?>
