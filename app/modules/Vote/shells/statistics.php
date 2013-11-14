<?php
class StatisticsShell extends NF_Shell {
    function main($argc, $argv) {
        load('inc/db');
        $db = DB::getInstance();

        self::line('write vote_week......', false);
        $week = strtotime(date("Y-m-d", time()-86400*7));
        $sql = "select vid,subject,num from pl_vote where start>$week and status=1 order by num desc,vid desc limit 10";
        $res = $db->all($sql);
        nforum_cache_write("vote_week", $res);
        self::line('done');

        self::line('write vote_month......', false);
        $week = strtotime(date("Y-m-01", time()));
        $sql = "select vid,subject,num from pl_vote where start>$week and status=1 order by num desc,vid desc limit 10";
        $res = $db->all($sql);
        nforum_cache_write("vote_month", $res);
        self::line('done');

        self::line('write vote_year......', false);
        $week = strtotime(date("Y-m-01", time()));
        $week = strtotime(date("Y-01-01", time()));
        $sql = "select vid,subject,num from pl_vote where start>$week and status=1 order by num desc,vid desc limit 10";
        $res = $db->all($sql);
        nforum_cache_write("vote_year", $res);
        self::line('done');
    }
}
