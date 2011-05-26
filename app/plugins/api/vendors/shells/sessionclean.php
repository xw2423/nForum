<?php
App::import("vendor", "db");
class SessioncleanShell extends Shell {
    public function main(){
        $db = DB::getInstance();
        $db->delete('pl_api_session', 'where expire<?', array(time()));
    }
}

