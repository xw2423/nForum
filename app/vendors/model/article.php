<?php
/****************************************************
 * FileName: app/vendors/model/article.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
App::import("vendor", array("model/archive", "model/code"));

/**
 * class Article is the single article in kbs
 *
 * @see FLAGS in get_article_flag() libBBS/article.c
 * @extends Archive
 * @author xw
 */
class Article extends Archive{

    /**
     * the reference of the board which article in
     * @var Board $_board
     */
    protected $_board;

    /**
     * in threads mode:
     *      the position in threads & start with zero
     * in non-sort mode:
     *      the position in list in current mode
     * @var int $_pos
     */
    protected $_pos;

    /**
     * function getInstance get a Article object via $board & $id
     * suggest using this method to get a ref of article
     * it will check the board mode to decide use which method to get article
     * if board is in non-sort mode ,article is got by its position
     *
     * @param int $id
     * @param Board $board
     * @return Article object
     * @static
     * @access public
     * @throws ArticleNullException
     */
    public static function getInstance($id, $board){
        if($board->isSortMode()){
            $info = array();
            $num = bbs_get_records_from_id($board->NAME, $id, 0, $info);
            if($num == 0){
                $num = bbs_get_records_from_id($board->NAME, $id, 11, $info);
                if($num == 0)
                    throw new ArticleNullException();
            }
            $info = $info[1];
        }else{
            $info = bbs_getarticles($board->NAME, $id, 1, $board->getMode());
            if(!is_array($info))
                throw new ArticleNullException();
            $info = $info[0];
        }
        return new Article($info, $board, $id);
    }

    /**
     * function post post a new article to board
     *
     * @param Board $board
     * @param string $sub subject
     * @param string $con content
     * @param int $sig signature
     * @param int $email 1:mail when has reply 0:no mail
     * @param int $anony 1:post with anonymous
     * @param int $outgo 1:outgo post
     * @param int $tex no use
     * @return int new article id
     * @static
     * @access public
     * @throws ArticlePostException with error code
     */
    public static function post($board, $sub, $con, $sig, $email = 0, $anony = 0, $outgo = 0, $tex = 0){
        $code = null;
        $ret = bbs_postarticle($board->NAME, $sub, $con, $sig, 0, $outgo, $anony, $email, $tex);
        switch($ret){
            case -1:
                $code = ECode::$BOARD_UNKNOW;
                break;
            case -2:
                $code = ECode::$POST_ISBOARD;
                break;
            case -3:
                $code = ECode::$POST_NOSUB;
                break;
            case -4:
                $code = ECode::$BOARD_READONLY;
                break;
            case -5:
                $code = ECode::$POST_BAN;
                break;
            case -6:
                $code = ECode::$POST_FREQUENT;
                break;
            case -7:
                $code = ECode::$SYS_INDEX;
                break;
            case -8:
                $code = ECode::$ARTICLE_NOREPLY;
                break;
            case -9:
                $code = ECode::$SYS_ERROR;
                break;
            case -10:
                $code = ECode::$POST_WAIT;
                break;
        }
        if(!is_null($code))
            throw new ArticlePostException($code);
        return $ret;
    }

    /**
     * function autoPost post file with deliver
     *
     * @param string $board
     * @param string $title
     * @param string $content
     * @return mixed article num suc|false fail
     * @static
     * @access public
     */
    public static function autoPost($board, $title, $content = ""){
        $file = tempnam(TMP . 'cache', "autopost");
        $fp = fopen($file,"w");
        $content = str_replace('\n', "\n", $content);
        fwrite($fp,"$content\n");
        fclose($fp);
        $ret = bbs_postfile($file,$board,$title);
        unlink($file);
        return ($ret >= 0)?$ret:false;
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
     * @param boolean $t only threads
     * @return array
     * @access public
     */
    public static function search($board, $t1, $t2, $tn, $author, $day, $m, $a, $t){
        $m = ($m)?1:0;
        $a = ($a)?1:0;
        $t = ($t)?1:0;
        $ret = bbs_search_articles($board->NAME, $t1, $t2, $tn, $author, intval($day), $m, $a, $t);
        if(!is_array($ret))
            return array();
        foreach($ret as &$v){
            $v = new Article($v, $board);
        }
        return $ret;
    }

    /**
     * function __contstruct()
     * do not use this to get a object
     *
     * @param array $info
     * @param Board $board
     * @param int $pos
     * @return Article
     * @access public
     * @throws ArticleNullException
     */
    public function __construct($info, $board, $pos = null){
        try{
            parent::__construct($info);
        }catch(ArchiveNullException $e){
            throw new ArticleNullException();
        }
        if(!is_a($board, "Board"))
            throw new ArticleNullException();
        $this->_board = $board;
        $this->_pos = $pos;
    }

    /**
     * function update update article
     *
     * @param string $title
     * @param string $content
     * @return boolean true|false
     * @access public
     * @override
     */
    public function update($title, $content){
        if(($ret = bbs_updatearticle2($this->_board->NAME, $this->ID, $title, $content, 0)) < 0)
            return false;
        return true;
    }

    /**
     * function reply similar to Article::post
     *
     * @param string $sub subject
     * @param string $con content
     * @param int $sig signature
     * @param int $email 1:mail when has reply 0:no mail
     * @param int $anony 1:post with anonymous
     * @param int $outgo 1:outgo post
     * @param int $tex no use
     * @return int new article id
     * @access public
     * @throws ArticlePostException with error code
     */
    public function reply($sub, $con, $sig, $email = 0, $anony = 0, $outgo = 0, $tex = 0){
        $code = null;
        $ret = bbs_postarticle($this->_board->NAME, $sub, $con, $sig, $this->ID, $outgo, $anony, $email, $tex);
        switch($ret){
            case -1:
                $code = ECode::$BOARD_UNKNOW;
                break;
            case -2:
                $code = ECode::$POST_ISBOARD;
                break;
            case -3:
                $code = ECode::$POST_NOSUB;
                break;
            case -4:
                $code = ECode::$BOARD_READONLY;
                break;
            case -5:
                $code = ECode::$POST_BAN;
                break;
            case -6:
                $code = ECode::$POST_FREQUENT;
                break;
            case -7:
                $code = ECode::$SYS_INDEX;
                break;
            case -8:
                $code = ECode::$ARTICLE_NOREPLY;
                break;
            case -9:
                $code = ECode::$SYS_ERROR;
                break;
            case -10:
                $code = ECode::$POST_WAIT;
                break;
        }
        if(!is_null($code))
            throw new ArticlePostException($code);
        return $ret;
    }

    /**
     * function delete remove single article
     *
     * @return boolean true|false
     * @access public
     * @override
     */
    public function delete(){
        $ret = bbs_delpost($this->_board->NAME, $this->ID);
        if($ret == -1 || $ret == -2)
            return false;
        return true;
    }

    /**
     * function forward mail this article to sb.
     *
     * @param string $target
     * @param boolean $noatt
     * @param boolean $noansi
     * @param boolean $big5
     * @return null
     * @access public
     * @throws ArticleForwardException
     */
    public function forward($target, $noatt = false, $noansi = false, $big5 = false){
        $code = null;
        $ret = bbs_doforward($this->_board->NAME, $this->FILENAME, $this->TITLE, $target, $big5, $noansi, $noatt);
        switch ($ret) {
            case -1:
            case -10:
                $code = ECode::$SYS_ERROR;
            case -7:
                $code = ECode::$ARTICLE_NONE;
            case -8:
                $code = ECode::$USER_NOID;
                break;
            case -11:
                $code = ECode::$BOARD_NONE;
                break;
        }
        if(!is_null($code))
            throw new ArticleForwardException($code);
    }

    public function getFileName(){
        return "boards" . DS . $this->_board->NAME . DS . $this->FILENAME;
    }

    public function getAttLink($pos){
        $mode = $this->_board->getMode();
        return "/{$this->_board->NAME}" . ($mode == Board::$THREAD?'':('/' . $mode)) . '/' . ($this->_board->isSortMode()?$this->ID:$this->_pos) . '/' . $pos;
    }

    /**
     * function getContent get the content(signature) of article
     * no title, no attachment data, but only for article
     *
     * @return string
     * @access public
     * @override
     */
    public function getContent(){
        return bbs_originfile($this->_board->NAME, $this->FILENAME);
    }

    public function addAttach($file, $fileName){
        $ret = bbs_attachment_add($this->_board->NAME, $this->ID, $file, $fileName);
        if(!is_array($ret))
            throw new ArchiveAttException(ECode::kbs2code($ret));
        return $ret;
    }

    public function delAttach($num){
        $ret = bbs_attachment_del($this->_board->NAME, $this->ID, $num);
        if(!is_array($ret))
            throw new ArchiveAttException(ECode::kbs2code($ret));
        return $ret;
    }

    public function hasEditPerm($user){
        return ($this->OWNER == $user->userid || $user->isBM($this->_board) || $user->isAdmin());
    }

    public function getPos(){
        return $this->_pos;
    }

    /**
     * function pre get the pre article
     *
     * @return mixed success Article|fail false
     * @access public
     */
    public function pre(){
        $info = array();
        $num = bbs_get_records_from_id($this->_board->NAME, $this->ID, 0, $info);
        if($num == 0)
            return false;
        return new Article($info[0], $this->_board);

    }

    /**
     * function next get the next article
     *
     * @return mixed success Article|fail false
     * @access public
     */
    public function next(){
        $info = array();
        $num = bbs_get_records_from_id($this->_board->NAME, $this->ID, 0, $info);
        if($num == 0)
            return false;
        return new Article($info[2], $this->_board);

    }

    /**
     * function tPre get the pre article in same threads
     *
     * @return mixed success Article|fail false
     * @access public
     */
    public function tPre(){
        $info = bbs_get_threads_from_id($this->_board->BID, $this->ID, 0 ,-1);
        if(!$info)
            return false;
        return new Article($info[0], $this->_board);
    }

    /**
     * function tNext get the next article in same threads
     *
     * @return mixed success Article|fail false
     * @access public
     */
    public function tNext(){
        $info = bbs_get_threads_from_id($this->_board->BID, $this->ID, 0 ,1);
        if(!$info)
            return false;
        return new Article($info[0], $this->_board);
    }

    /**
     * function manage set article flag
     *
     * @param int $op
     *     0: do nth;
     *     1: del;
     *     2: mark;
     *     3: digest;
     *     4: noreplay;
     *     5: zhiding;
     *     7-9: % X #
     */
    public function manage($op, $top = false){
        $code = null;
        $ret = bbs_bmmanage($this->_board->NAME, $this->ID, $op, $top?1:0);
        switch ($ret) {
            case -1:
                $code = ECode::$BOARD_NONE;
                break;
            case -2:
                $code = ECode::$ARTICLE_NOMANAGE;
                break;
            case -3:
            case -9:
                $code = ECode::$SYS_ERROR;
                break;
            case -4:
                $code = ECode::$ARTICLE_NONE;
                break;
            default:
                break;
        }
        if(!is_null($code))
            throw new ArticleManageException($code);
        if($top || $op == 5){
            touch('boards/' . $this->_board->NAME . '/.DIR');
        }
    }

    public function addDeny($reason, $day){
        $this->_board->addDeny('', $reason, $day, $this->ID);
    }

    public function getBoard(){
        return $this->_board;
    }

    public function isTop(){
        return (strtolower($this->FLAGS[4]) == "d");
    }

    public function isRead(){
        return (strtolower($this->FLAGS[0]) != "n");
    }

    public function isSubject(){
        return ($this->ID == $this->GROUPID);
    }

    public function isCopy(){
        return ($this->O_BID == 0);
    }

    public function isReallyTop(){
        $a = array();
        return (bbs_get_records_from_id($this->_board->NAME, $this->ID, 11, $a) != 0);
    }

}

class ArticleNullException extends Exception {}
class ArticlePostException extends Exception {}
class ArticleDeleteException extends Exception {}
class ArticleForwardException extends Exception {}
class ArticleManageException extends Exception {}
?>
