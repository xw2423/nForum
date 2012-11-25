<?php
/****************************************************
 * FileName: app/vendors/model/widget.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
App::import("vendor", array("model/board", "model/section", "model/favor", "inc/db"));

/**
 * class Widget
 * widget is divided to core widget and ext widget.
 * core widget is from board, section, favor.
 * ext widget is in the same name php file in directory vendor/widget
 * the name of wigdget is like "[core-]name", if it is core widget it should be add core prefix like "board-Flash".
 * the name is the variable that can be used to create the object.
 * the ext widget only has a name that mapped to the class nameWidget, name will be lower.
 * And if the ext widget extend from WidgetAdapter in vendor/model/iwidget.php,it will help you to return widget name.
 *
 * @author xw
 */
class Widget {

    public static $table = "widget";
    public static $ADD = 0;
    public static $DELETE = 1;
    public static $MOVE = 2;
    public static $MODIFY = 4;
    public static $maxCol = 3;
    private static $_bound;
    private static $_instance = array();

    /**
     * function getInstance get a widget object with its name
     *
     * @param string $name
     * @return Widget object
     * @static
     * @access public
     * @throws WidgetNullException
     */
    public static function getInstance($name, $cache = true){
        $wid = $name;
        if(isset(self::$_instance[$wid]))
            return self::$_instance[$wid];
        $core = Configure::read("widget.core");
        $ext = Configure::read("widget.ext");
        $names = split('-', $name, 2);
        $name = strtolower($names[0]);
        if(!in_array($name, $core)){
            $in = false;
            foreach($ext as $v){
                if(in_array($name, $v[1])){
                    $in = true;
                    if(!App::import('vendor', 'widget/' . $name)
                        && isset($v[2])){
                        foreach($v[2] as $file)
                            App::import('vendor', 'widget/' . $file);
                    }
                    break;
                }
            }
            if(!$in)
                throw new WidgetNullException("no such widget");
            $className = $name . "Widget";
            if(!class_exists($className))
                throw new WidgetNullException("no such widget");
            $obj = new $className();
        }else{
            try{
                $name = ucfirst($name);
                $obj = call_user_func(array($name, "getInstance"), $names[1]);
            }catch(Exception $e){
                throw new WidgetNullException("no such widget");
            }
        }
        if($cache) self::$_instance[$wid] = $obj;
        return $obj;
    }

    /**
     * function wInit create widget via default
     *
     * @param User $user
     * @return array default setting
     * @static
     * @access public
     */
    public static function wInit($user){
        try{
            $uid = $user->userid;
            $db = DB::getInstance();
            $default = Configure::read("widget.default");
            foreach($default as $k=>$v){
                $w = self::getInstance($k);
                $color = isset($v["color"])?$v["color"]:0;
                $title = !empty($v["title"])?$v["title"]:$w->wGetTitle();
                $arr[] = array("null", $uid, $w->wGetName(), $title['text'], $color, $v["col"], $v["row"]);
                $ret[] = array(
                    "color"=> $color,
                    "title"=> Sanitize::html($title['text']),
                    "url"=> $title['url'],
                    "wid"=>$w->wGetName(),
                    "col" => $v['col'],
                    "row" => $v['row']

                );
            }
            $val = array("v"=> $arr);
            $db->insert(self::$table, $val);
        }catch(Exception $e){
            return array();
        }
        return (array)$ret;
    }

    /**
     * function wGet get user widget
     * it just get the properties of widget, no content
     *
     * @param User $user
     * @return array('color', 'title', 'name', 'url', 'col', 'row')
     * @static
     * @access public
     */
    public static function wGet($user){
        App::import('Sanitize');
        $colors = Configure::read("widget.color");
        $uid = $user->userid;
        $ret = array();
        if($uid == 'guest'){
            $widgets = Configure::read("widget.default");
            foreach($widgets as $name=>$v){
                try{
                    $ww = Widget::getInstance($name);
                    $title = $ww->wGetTitle();
                }catch(WidgetNullException $e){
                    $title = array('text' => 'WIDGET MISS' ,'url' => '');
                    //it will show widget name,title
                    //but show error in content
                }
                $color = isset($v["color"])?$v["color"]:0;
                $title = isset($v["title"])?$v["title"]:$title;
                $ret[] = array("color" => $colors[$color][0]
                    ,"title" => Sanitize::html($title['text'])
                    ,"url" => $title['url']
                    ,"name" => $name
                    ,"col" => $v['col']
                    ,"row" => $v['row']
                );
            }
        }else{
            //two columns
            $two = ($user->getCustom("userdefine1", 31) == 0)?" and (col=1 or col=2)":"";
            $db = DB::getInstance();
            $sql = "select * from " . self::$table . " where uid=? {$two} order by col,row";
            $res = $db->all($sql, array($uid));
            if(empty($res))
                $res = self::wInit($user);
            foreach((array)$res as $v){
                try{
                    $title = self::getInstance($v['wid'])->wGetTitle();
                }catch(WidgetNullException $e){
                    $title = array('text' => 'WIDGET MISS' ,'url' => '');
                }
                $ret[] = array("color" => $colors[$v['color']][0]
                    ,"title"=> empty($v['title'])?$title['title']:Sanitize::html($v['title'])
                    ,"name" => $v['wid']
                    ,"url" => $title['url']
                    ,"col" => $v['col']
                    ,"row" => $v['row']
                );
            }
        }
        return (array)$ret;
    }

    /**
     * function wAdd
     * you should valid wid
     *
     * @param User user
     * @param string $wid
     * @param string $title
     * @param int $color
     * @param int $col
     * @param int $row
     * @static
     * @access public
     * @throws WidgetOpException
     */
    public static function wAdd($user, $wid, $title, $color, $col, $row){
        $uid = $user->userid;
        if(self::_validWidget($uid, $wid))
            throw new WidgetOpException("add error");
        if(!self::_validCol($col))
            throw new WidgetOpException("add out of bound");
        $maxRow = self::_getRow($uid, $col);
        if($row < 1 || $row > $maxRow + 1)
            throw new WidgetOpException("add out of bound");

        try{
            $db = DB::getInstance();
            $update = array("\\row"=>"row+1");
            $where = "where col=? and row>=? and uid=?";
            $db->update(self::$table, $update, $where, array($col, $row, $uid));
            $val = array("v"=> array(array("null", $uid, $wid, $title, $color, $col, $row)));
            $db->insert(self::$table, $val);
        }catch(Exception $e){
            throw new WidgetOpException("add error");
        }
    }

    /**
     * function wDelete
     *
     * @param User user
     * @param string $wid
     * @static
     * @access public
     * @throws WidgetOpException
     */
    public static function wDelete($user, $wid){
        $uid = $user->userid;

        try{
            $db = DB::getInstance();
            $sql = "select count(*) as num from " . self::$table . " where uid=?";
            $ret = $db->one($sql, array($uid));
            if($ret['num'] == 1)
                throw new WidgetOpException("delete last widget");
            $sql = "select col,row from " . self::$table . " where uid=? and wid=?";
            $ret = $db->one($sql, array($uid, $wid));
        }catch(DBException $e){
            throw new WidgetOpException("delete no widget");
        }
        if(!$ret)
            throw new WidgetOpException("delete no widget");
        $col = $ret['col'];
        $row = $ret['row'];

        try{
            $where = "where wid=? and uid=?";
            $db->delete(self::$table, $where, array($wid, $uid));
            $update = array("\\row"=>"row-1");
            $where = "where col=? and row>? and uid=?";
            $db->update(self::$table, $update, $where, array($col, $row, $uid));
        }catch(Exception $e){
            throw new WidgetOpException("delete error");
        }
    }

    /**
     * function wMove
     *
     * @param User user
     * @param string $wid
     * @param int $ncol
     * @param int $nrow
     * @static
     * @access public
     * @throws WidgetOpException
     */
    public static function wMove($user, $wid, $ncol, $nrow){
        //get widget pos
        $ret = false;
        $uid = $user->userid;
        try{
            $db = DB::getInstance();
            $sql = "select col,row from " . self::$table . " where uid=? and wid=?";
            $ret = $db->one($sql, array($uid, $wid));
        }catch(DBException $e){
            throw new WidgetOpException("move error");
        }
        if($ret === false)
            throw new WidgetOpException("move no widget");
        $ocol = $ret['col'];
        $orow = $ret['row'];

        //no move
        if($ocol == $ncol && $orow == $nrow)
            return;

        //valid bound
        if(!self::_validCol($ocol) || !self::_validCol($ncol))
            throw new WidgetOpException("move out of bound");
        $maxoRow = self::_getRow($uid, $ocol);
        if($orow < 1 || $orow > $maxoRow)
            throw new WidgetOpException("move out of bound");
        $maxnRow = self::_getRow($uid, $ncol);
        if($ocol == $ncol && ($nrow < 1 || $nrow > $maxnRow)
            || $ocol != $ncol && ($nrow < 1 || $nrow > $maxnRow + 1))
            throw new WidgetOpException("move out of bound");

        //operate
        try{
            if($ocol == $ncol){
                $min = ($orow > $nrow)?$nrow:$orow;
                $max = ($orow < $nrow)?$nrow:$orow;
                $op = ($orow > $nrow)?"+":"-";
                $db->update(self::$table, array("\\row"=>"row{$op}1"), "where col=? and row>=? and row<=? and uid=?", array($ocol, $min, $max, $uid));
            }else{
                $update = array("\\row"=>"row+1");
                $where = "where col=? and row>=? and uid=?";
                $db->update(self::$table, $update, $where, array($ncol, $nrow, $uid));

                $update = array("\\row"=>"row-1");
                $where = "where col=? and row>? and uid=?";
                $db->update(self::$table, $update, $where, array($ocol, $orow, $uid));
            }
            $update = array("row"=>$nrow, "col"=>$ncol);
            $where = "where wid=? and uid=?";
            $db->update(self::$table, $update, $where, array($wid, $uid));

        }catch(Exception $e){
            throw new WidgetOpException("move error");
        }
    }

    /**
     * function wSet
     *
     * @param User user
     * @param string $wid
     * @param int $ncol
     * @param int $nrow
     * @static
     * @access public
     * @throws WidgetOpException
     */
    public static function wSet($user, $wid, $title, $color){
        $uid = $user->userid;
        try{
            if(!self::_validWidget($uid, $wid))
                throw new WidgetOpException("set no widget");
            $db = DB::getInstance();
            $val = array("title"=>$title, "color"=>$color);
            $where = "where wid=? and uid=?";
            $db->update(self::$table, $val, $where, array($wid, $uid));
        }catch(DBException $e){
            throw new WidgetOpException("set error");
        }
    }

    public static function w3to2($user){
        $uid = $user->userid;
        try{
            $row1 = self::_getRow($uid, 1);
            $row2 = self::_getRow($uid, 2);
            $row3 = self::_getRow($uid, 3);
            $i = $row3;$r1 = $r2 = 0;
            while($i > 0){
                ($r1 <= $r2)?$r1++:$r2++;
                $i--;
            }
            $db = DB::getInstance();
            $val = array("\\row"=>"row+".$row1, "col"=>"1");
            $where = "where uid=? and col=3 and row>=1 and row<=?";
            $db->update(self::$table, $val, $where, array($uid, $r1));

            $val = array("\\row"=>"row+".($row2-$r1), "col"=>"2");
            $where = "where uid=? and col=3 and row>?";
            $db->update(self::$table, $val, $where, array($uid, $r1));
        }catch(Exception $e){
            throw new WidgetOpException("set error");
        }
    }

    public static function html($val, $style = "tab"){
        $ret = "";
        $base = Configure::read("site.prefix");
        switch($style){
            case "tab":
                if(!isset($val["s"])){
                    $ret .= '<div class="w-tab"><div class="w-tab-title"><ul>';
                    $li = $t_con = "";
                    foreach($val as $k=>$v){
                        $li .= ('<li _index="'. $k .'" class="tab-normal'. (($k == 0)?' tab-down"':'') . '">' . $v['t'] . '</li>');
                        $t_con .= ('<div class="w-tab-content w-tab-' . $k . '"' . (($k ==0)?' style="display:block"':''). '>' . self::html($v['v']) . '</div>');
                    }
                    $ret .= ($li . '</ul></div>' . $t_con . "</div>");
                }else{
                    $ret = self::html($val['v'], $val['s']);
                }
                break;
            case WidgetAdapter::$S_FREE:
                $ret .= '<div class="' . $style . '">';
                $ret .= $val[0]['text'];
                $ret .= '</div>';
                break;
            case WidgetAdapter::$S_LINE:
            case WidgetAdapter::$S_FLOAT:
                $ret .= '<ul class="' . $style . '">';
                foreach((array)$val as $v){
                    $ltext = $v['text'];
                    $otext = preg_replace("/<[\s\S]*?>([\s\S]*?)<\/[\s\S]*?>|<[\s\S]*?\/>/", "\\1", $ltext);
                    if(empty($v['url']))
                        $ret .= ('<li>' . $ltext . '</li>');
                    else
                        $ret .= ('<li title="' . $otext . '"><a href="' . $base . $v['url'] . '">' . $ltext . '</a></li>');
                }
                $ret .= '</ul>';
                break;
        }
        return $ret;
    }

    private static function _validCol($col){
        if($col > self::$maxCol || $col < 1)
            return false;
        return true;
    }

    private static function _getRow($uid, $col){
        if(!isset(self::$_bound)){
            $db = DB::getInstance();
            $sql = "select col,max(row) as row from " . self::$table . " where uid=? group by col order by col";
            $res = $db->all($sql, array($uid));
            self::$_bound = array_fill(1 ,self::$maxCol ,0);
            foreach($res as $v){
                self::$_bound[$v['col']] = $v['row'];
            }
        }
        return self::$_bound[$col];
    }

    private static function _validWidget($uid, $wid){
        $db = DB::getInstance();
        $sql = "select wid from " . self::$table . " where uid=?";
        return $db->find($wid, $sql, array($uid));
    }
}

class WidgetNullException extends Exception{}
class WidgetOpException extends Exception{}
?>
