<?php
/****************************************************
 * FileName: app/vendors/model/iwidget.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/

/**
 * interface iWidget define the method of widget
 *
 * @author xw
 */
interface iWidget{

    /**
     * get widget name
     * @return String
     */
    public function wGetName();

    /**
     * get widget title
     * if no url set it to ''
     * @return Array array("text"=>"", url=>"")
     */
    public function wGetTitle();

    /**
     * get widget content
     * @return Array
     */
    public function wGetList();

    /**
     * get widget modify time
     * @return int
     */
    public function wGetTime();

    /**
     * whether user has permit to access widget
     * @return boolean
     */
    public function wHasPerm($u);
}

/**
 * widget adapter
 *
 * //get widget name
 * public function wGetName()
 * //get widget title
 * public function wGetTitle()
 * //get widget content(array)
 * public function wGetList()
 * //get widget modify time cache
 * public function wGetTime()
 *
 * style list:w-free w-tab w-list-line w-list-float
 * A=[{t:w-tab, v:A|B}+]|B
 * B={s:w-free|w-list-line|w-list-float, v:[C+]}
 * C={text:"", url:""}

 * A=array(array("t"=>"","v"=>A|B)+)|B
 * B=array("s"=>STYLE,"v"=>array(C+))
 * STYLE=w-free|w-list-line|w-list-float
 * C=array("text"=>"", "url"=>"")
 */
abstract class WidgetAdapter implements iWidget{
    public static $S_FREE = "w-free";
    public static $S_LINE ="w-list-line";
    public static $S_FLOAT = "w-list-float";
    //no tab actually
    public static $S_TAB = "w-tab";

    public function wGetName(){
        return substr(get_class($this), 0, -6);
    }

    public function wGetTitle(){
        return array("text"=>"adapter", "url"=>"");
    }

    public function wGetList(){
        return $this->_error('this is a widget adapter');
    }

    //i set an new time default, if visit widget by ajax ,it will update immediately
    public function wGetTime(){
        return time();
    }

    //all user can access default
    public function wHasPerm($u){
        return true;
    }

    protected function _error($msg){
        return array("s"=>self::$S_LINE, "v"=>array(array("text"=>$msg, "url"=>"")));
    }
}

/**
 * class EWideget
 * use for show error when widget has some error
 *
 * @extends WidgetAdapter
 * @author xw
 */
class EWidget extends WidgetAdapter {

    private $_err = '该应用不存在或被关闭';
    public function __construct($err = ''){
        if(!empty($err)) $this->_err = $err;
    }
    //the two functions below is useless , i will not use them
    public function wGetName(){return '';}
    public function wGetTitle(){return array("text"=>"Error", "url"=>"");}

    //it will update content by ajax with cache immediately
    //so if no error, you will update widget time to a new time
    public function wGetTime(){return parent::wGetTime();}
    public function wGetList(){return $this->_error($this->_err);}
}
?>
