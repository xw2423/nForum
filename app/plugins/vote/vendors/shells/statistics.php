<?php
App::import("vendor", "db");
class StatisticsShell extends Shell {
    function main() {
        $db = DB::getInstance();
        $week = strtotime(date("Y-m-d", time()-86400*7));
        $sql = "select vid,subject,num from pl_vote where start>$week and status=1 order by num desc,vid desc limit 10";
        $res = $db->all($sql);
        nforum_cache_write("vote_week", $res);
        $week = strtotime(date("Y-m-01", time()));
        $sql = "select vid,subject,num from pl_vote where start>$week and status=1 order by num desc,vid desc limit 10";
        $res = $db->all($sql);
        nforum_cache_write("vote_month", $res);
        $week = strtotime(date("Y-01-01", time()));
        $sql = "select vid,subject,num from pl_vote where start>$week and status=1 order by num desc,vid desc limit 10";
        $res = $db->all($sql);
        nforum_cache_write("vote_year", $res);
    }
}
?>
