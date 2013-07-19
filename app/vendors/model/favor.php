<?php
/****************************************************
 * FileName: app/vendors/model/favor.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
App::import("vendor", array("model/collection", "model/iwidget"));

/**
 * class Favor
 * if bid is -1 the favor is dir
 * BID is number to access
 * NPOS is number to delete
 *
 * @extends Collection
 * @implements iWidget
 * @author xw
 * @todo:bbs_fav_boards new mode
 */
class Favor extends Collection implements iWidget {

    public static $BOARD = 0;
    public static $DIR = 1;

    private $_level;

    /**
     * function getInstance get a Favor object via favor level
     *
     * @param int $level
     * @return Favor object
     * @static
     * @access public
     * @throws FavorNullException
     */
    public static function getInstance($level = 0){
        //1 is for normal what about 2?
        if(bbs_load_favboard($level, 1) == -1)
            throw new FavorNullException();
        $info = bbs_fav_boards_nforum($level, 1);
        if(!$info)
            throw new FavorNullException();
        try{
            return new Favor($info, $level);
        }catch(CollectionNullException $e){
            throw new FavorNullException();
        }
    }

    public function wGetName(){
        return "favor-" . $this->_level;
    }

    public function wGetTitle(){
        return array("text"=>"个人定制区", "url"=>"/favor");
    }

    public function wGetList(){
        $arr = array();
        if(!$this->isNull()){
            $brds = $this->getList();
            foreach($brds as $v){
                $arr[] = array("text"=>$v->DESC, "url"=>"/board/{$v->NAME}");
            }
        }
        if(empty($arr))
            return array("s"=>"w-list-line", "v"=>array(array("text"=>"不存在任何版面", "url"=>"")));
        else
            return array("s"=>"w-list-float", "v"=>$arr);
    }

    public function wGetTime(){
        $u = User::getInstance();
        $file = $u->getHome('favboard');
        if (!file_exists($file)) {
            return time();
        }
        return filemtime($file);
    }

    public function wHasPerm($u){
        return true;
    }

    public function isRoot(){
        return ($this->_level == 0);
    }

    public function getParent(){
        if($this->isRoot())
            return null;
        try{
            return self::getInstance(bbs_get_father($this->_level));
        }catch(FavorNullException $e){
            return null;
        }
    }

    /**
     * function add add a board or directory to favor in current level
     *
     * @param mixed $v board or directory name
     * @param int $mode
     * @return boolean true|false
     * @access public
     */
    public function add($v, $mode = 0){
        if($mode == self::$DIR)
            $ret = bbs_add_favboarddir($v);
        else
            $ret = bbs_add_favboard($v->NAME);
        if($ret == -1)
            return false;
        return true;
    }

    /**
     * function add add a board or directory to favor in current level
     *
     * @param mixed $v (bid-1) or directory name
     * @param int $mode
     * @return boolean true|false
     * @access public
     */
    public function delete($v, $mode = 0){
        if($mode == self::$DIR)
            $ret = bbs_del_favboarddir($this->_level, intval($v));
        else
            $ret = bbs_del_favboard($this->_level, $v->BID - 1);
        if($ret == -1)
            return false;
        return true;
    }

    public function getLevel(){
        return $this->_level;
    }

    /**
     * function __construct
     * if $info number is 1 and bid is -1(dir) and NPOS is -1(invalid)
     * the favor is null
     *
     * @param array $info
     * @param int $level
     * @return Favor object
     * @access protected
     */
    protected function __construct($info, $level){
        if(count($info) == 1 && $info[0]['BID'] == -1 && $info[0]['NPOS'] == -1)
            $info = array();
        parent::__construct($info);
        $this->_level = $level;
    }
}

class FavorNullException extends Exception {}
?>
