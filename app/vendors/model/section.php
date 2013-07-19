<?php
/****************************************************
 * FileName: app/vendors/model/section.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
App::import("vendor", array("model/collection", "model/iwidget", "model/board"));

/**
 * class Section can be get from num & board which is directory
 *
 * @author xw
 */
class Section extends collection implements iWidget{

    public static $NORMAL = 2;
    public static $ALL = 4;

    /**
     * the section number of this section
     * if root section it is section number
     * if board section it is the sectoin number the board in
     * @var int $_num
     */
    private $_num;

    /**
     * the section description of this section
     * if board section it is board name
     * @var desc $_desc
     */
    private $_desc;

    /**
     * whether section is root section
     * @var boolean $_root
     */
    private $_root;

    /**
     * the reference of board
     * if root section $_board is null
     * @var Board $_board
     */
    private $_board = null;

    /**
     * function constructor for section
     *
     * @param mixed    $mixed string section number|Board object|board name
     * @param int $mode Section static variable.default get all the board
     * @static
     * @access public
     */
    public static function getInstance($mixed, $mode = 4){
        $info = array();
        if(is_object($mixed) && is_a($mixed, "Board") && $mixed->isDir()){
            $info = bbs_getboards_nforum($mixed->SECNUM, $mixed->BID, $mode);
            $desc = $mixed->DESC;
            $root = false;
        }else if(preg_match("/^(\w|\d+)$/", strval($mixed))){
            $mixed = strval($mixed);
            $secs = Configure::read('section');
            $secode = array_keys($secs);
            foreach($secode as &$v) $v=strval($v);
            if(!in_array($mixed, $secode, true))
                throw new SectionNullException();
            $info = bbs_getboards_nforum($mixed, 0, $mode);
            $desc = $secs[$mixed][0];
            $root = true;
        }else{
            try{
                $mixed = Board::getInstance($mixed);
                if(!$mixed->isDir())
                    throw new SectionNullException();
                $info = bbs_getboards_nforum($mixed->SECNUM, $mixed->BID, $mode);
                $desc = $mixed->DESC;
                $root = false;
            }catch(BoardNullException $e){
                throw new SectionNullException();
            }
        }
        try{
            return new Section($info, $mixed, $desc, $root);
        }catch(CollectionNullException $e){
            throw new SectionNullException();
        }
    }

    public function isRoot(){
        return $this->_root;
    }

    public function getParent(){
        try{
            if($this->isRoot())
                return null;
            if($this->_board->GROUP == 0)
                return self::getInstance($this->_num);
            else
                return self::getInstance(Board::getInstance($this->_board->GROUP));
        }catch(SectionNullException $e){
            return null;
        }catch(BoardNullException $e){
            return null;
        }
    }

    public function wGetName(){
        return "section-" . $this->getName();
    }

    public function wGetTitle(){
        return array("text"=>$this->getDesc(), 'url'=>'/section/'.$this->getName());
    }

    public function wGetList(){
        $arr = array();
        $brds = $this->getList();
        if(empty($brds)){
            $arr[] = array("text"=>ECode::msg(ECode::$SEC_NOBOARD), "url"=>"");
            $arr = array("s"=>"w-list-line", "v"=>$arr);
        }else{
            foreach($brds as $v){
                $arr[] = array("text"=>$v->DESC, "url"=>"/board/{$v->NAME}");
            }
            $arr = array("s"=>"w-list-float", "v"=>$arr);
        }

        if($this->isRoot()){
            $file = BBS_HOME . "/xml/day_sec{$this->_num}.xml";
            $ret = array();
            if (file_exists($file)) {
                $xml = simplexml_load_file($file);
                if($xml !== false){
                    foreach($xml->hotsubject as $v){
                        $title = nforum_fix_gbk(urldecode($v->title));
                        try{
                            $brd = Board::getInstance(urldecode($v->board));
                            $ret[] = array("text" => '[<a style="color:blue" href="'. Configure::read("site.prefix") . "/board/" . $brd->NAME.'">' . $brd->DESC . '</a>] <a title="'. $title. '" href="'.Configure::read("site.prefix").'/article/' . $v->board . "/" . $v->groupid .'">'. $title . '</a>', "url"=> "");
                        }catch(BoardNullException $e){
                        }
                    }
                }
            }
            if(empty($ret))
                $ret[] = array("text" => ECode::msg(ECode::$SEC_NOHOT), "url" => "");
            $ret = array("s"=>"w-list-line", "v"=>$ret);

            return array(array("t"=>"热门话题", "v"=>$ret), array("t"=>"版面列表", "v"=>$arr));
        }else{
            return $arr;
        }
    }

    public function wGetTime(){
            return time();
        if($this->isRoot()){
            $file = BBS_HOME . "/xml/day_sec{$this->_num}.xml";
            if (!file_exists($file)) {
                return time();
            }
            return filemtime($file);
        }else{
            return time();
        }
    }

    public function wHasPerm($u){
        return true;
    }

    /**
     * function getName get the id string of section
     * root section is its number
     * board section is its NAME
     *
     * @return string
     * @access public
     */
    public function getName(){
        return ($this->isRoot()?$this->_num:$this->_board->NAME);
    }

    public function getDesc(){
        return $this->_desc;
    }

    /**
     * function getPos get num for favorate delete
     * root section return 0
     * board section return its NPOS
     *
     * @return int
     * @access public
     */
    public function getNPos(){
        return ($this->isRoot()?0:$this->_board->NPOS);
    }

    protected function __construct($info, $mixed, $desc, $root){
        parent::__construct($info);
        $this->_num = $root?$mixed:$mixed->SECNUM;
        $this->_desc = $desc;
        $this->_root = $root;
        if(!$root)
            $this->_board = $mixed;
    }
}
class SectionNullException extends Exception {}
?>
