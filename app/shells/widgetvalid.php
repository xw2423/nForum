<?php
/**
 * check invalid widget postion
 * using sql below to remove same one
 *
 * drop table tmp; create table tmp as SELECT id FROM `widget` GROUP BY `uid` , `col` , `row` HAVING count( * ) >1;
 * delete from widget where id in (select id from tmp);
 * drop table tmp;
 *
 * @extends xw
 * @implements xw
 * @author xw
 */

class WidgetValidShell extends NF_Shell {
    public function main($argc, $argv){
        load('inc/db');
        $db = DB::getInstance();
        $ret = $db->one("select count(*) as num from widget");
        $num = $ret['num'];
        $start = 0;$step = 100000;
        while($start<$num){
            $sql = "SELECT uid,col,row FROM `widget` order by uid,col,row limit $start,$step";
            $ret = $db->all($sql);
            $id = $col = "";
            $row = -100;
            $wrong = false;
            foreach($ret as $v){
                if($id != $v['uid']){
                    $row = 1;
                    $id = $v['uid'];
                    $wrong = false;
                    self::line("check $id\n");
                }
                if($col != $v['col']){
                    $row = 1;
                    $col = intval($v['col']);
                    $wrong = false;
                }
                if($row != intval($v['row']) && $row != -100 && !$wrong){
                    self::line("$id\t$col\terror");
                    $row = -100;
                    $wrong = true;
                }
                $row ++;
            }
            unset($ret);
            $start += $step;
        }
    }
}
