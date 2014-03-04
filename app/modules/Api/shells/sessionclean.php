<?php
class SessioncleanShell extends NF_Shell {
    public function main($argc, $argv){
        load('inc/db');
        $db = DB::getInstance();
        $db->delete('pl_api_session', 'where expire<?', array(time() - 86400));
        self::line('done');
    }
}

