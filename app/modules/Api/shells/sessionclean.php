<?php
class SessioncleanShell extends NF_Shell {
    public function main($argc, $argv){
        load('inc/db');
        $db = DB::getInstance();
        $expire = c('modules.api.expire');
        if(null === $expire) $expire = 1200;
        $db->delete('pl_api_session', 'where expire<?', array(time() - $expire));
        self::line('done');
    }
}

