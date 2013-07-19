<?php
/****************************************************
 * FileName: app/vendors/inc/pagination.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/

/**
 * interface Pageable
 *
 * @author xw
 */
interface Pageable {

    /**
     * function getTotalNum return total number of model
     *
     * @return int
     * @access public
     */
    public function getTotalNum();

    /**
     * function getRecord return range of model
     * attention! the begin index is 1
     *
     * @param int $start
     * @param int $num
     * @return array if failed return array()
     * @access public
     */
    public function getRecord($start, $num);
}

/**
 * class Pagination
 * Pagination can auto page Pageable object
 *
 * @author xw
 */
class Pagination {
    public static $NUMBER = 30;
    public static $OMIT = "...";
    public static $LINK = "#";

    /* must be a odd*/
    public static $MIDNUM = 7;

    /**
     * object to be paged
     * @var Pageable $_model
     */
    private $_model;

    /**
     * number per page
     * @var int $_num
     */
    private $_num;

    /**
     * totol number of $_model
     * @var int $_total
     */
    private $_total;

    /**
     * totol Page
     * @var int $_totalPage
     */
    private $_totalPage;

    /**
     * current page
     * @var int $_totalPage
     */
    private $_curPage;

    /**
     * number of element in current page,if the last page may be not equal $this->_num
     * @var int $_curNum
     */
    private $_curNum;

    /**
     * link format, %page% is page number
     * @var string $_link
     */
    private $_link;

    /**
     * omit format
     * @var string $_omit
     */
    private $_omit;

    /**
     * function __construct get a Pagination via $model and $num
     * $model must implements Pageable
     * $num will be Pagination::$NUMBER default
     *
     * @param Pageable $model
     * @param int $num
     * @return Pagination object
     * @access public
     * @throws CanNotPageableException
     */
    public function __construct($model, $num = null){
        $this->_num = self::$NUMBER;
        if(!is_a($model ,"Pageable"))
            throw new CanNotPageableException();
        $this->_model = $model;
        if(is_int($num) && $num > 0)
            $this->_num = $num;
        $this->_total = $this->_model->getTotalNum();
        if($this->_total == 0) $this->_totalPage = 1;
        else $this->_totalPage = intval(ceil($this->_total / $this->_num));
    }

    /**
     * function getPage get array of $page
     *
     * @param int $page
     * @return array
     * @access public
     */
    public function getPage($page){
        $this->_validPage($page);
        $this->_curPage = $page;
        $num = $this->_num;
        if($this->_totalPage == $page){
            $num = $this->_total - ($page - 1) * $this->_num;
        }
        $this->_curNum = $num;
        $start = ($page - 1) * $this->_num + 1;

        return $this->_model->getRecord($start, $num);
    }

    /**
     * function getPageBar get control bar of $page
     *
     * @param int $page
     * @param string $link default Pagination::$LINK
     * @param string $omit default Pagination::$OMIT
     * @param boolean $html return array of bar
     * @return array
     * @access public
     */
    public function getPageBar($page, $link = null, $omit = null, $html = true){
        $this->_omit = self::$OMIT;
        $this->_link = self::$LINK;
        if(is_string($link))
            $this->_link = $link;
        if(is_string($omit))
            $this->_omit = $omit;
        $this->_validPage($page);
        $tags = $this->_getTags($page);
        if(!$html)
            return $tags;
        $ret = "";
        if($page > 1){
            $link = str_replace("%page%", $page - 1, $this->_link);
            $ret .= "<li class=\"page-normal\"><a href=\"$link\" title=\"上一页\"><<</a></li>";
        }
        foreach($tags as $v){
            if($v == $page){
                $ret .= "<li class=\"page-select\"><a title=\"当前页\">$v</a></li>";
            }else if($v == $this->_omit){
                $ret .= "<li class=\"page-omit\">$v</li>";
            }else{
                $link = str_replace("%page%", $v, $this->_link);
                $ret .= "<li class=\"page-normal\"><a href=\"$link\" title=\"转到第{$v}页\">$v</a></li>";
            }
        }
        if($page < $this->_totalPage){
            $link = str_replace("%page%", $page + 1, $this->_link);
            $ret .= "<li class=\"page-normal\"><a href=\"$link\" title=\"下一页\">>></a></li>";
        }
        return $ret;
    }

    public function getTotalPage(){
        return $this->_totalPage;
    }

    public function getCurPage(){
        return $this->_curPage;
    }

    public function getTotalNum(){
        return $this->_total;
    }

    public function getCurNum(){
        return $this->_curNum;
    }

    private function _getTags($mid) {
        if($this->_totalPage <= self::$MIDNUM + 2)
            return range(1, $this->_totalPage);
        if($mid < (self::$MIDNUM + 3) / 2)
            $mid = (self::$MIDNUM + 3) / 2;
        if($mid > $this->_totalPage - (self::$MIDNUM + 1) / 2)
            $mid = $this->_totalPage - (self::$MIDNUM + 1) / 2;
        $range = range($mid - (self::$MIDNUM - 1) / 2, $mid + (self::$MIDNUM - 1) / 2);
        $ret = array(1);
        if($mid != (self::$MIDNUM + 3) / 2){
            $ret[] = $this->_omit;
        }
        $ret = array_merge($ret, $range);
        if($mid != $this->_totalPage - (self::$MIDNUM + 1) / 2){
            $ret[] = $this->_omit;
        }
        $ret[] = $this->_totalPage;

        return $ret;
    }

    private function _validPage(&$page){
        $page = intval($page);
        if($page < 1){
            $page = 1;
        }
        if($page > $this->_totalPage){
            $page = $this->_totalPage;
        }
    }

}

class CanNotPageableException extends Exception{}

/**
 * class ArrayPageableAdapter
 *
 * @author xw
 */
class ArrayPageableAdapter implements Pageable{

    private $_arr = array();

    public function __construct($arr){
        if(!is_array($arr))
            throw new CanNotPageableException();
        $this->_arr = $arr;
    }

    public function getTotalNum(){
        return count($this->_arr);
    }

    public function getRecord($start, $num){
        return array_slice($this->_arr, $start - 1, $num);
    }
}
?>
