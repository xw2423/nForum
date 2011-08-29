<?php
class friendController extends MobileAppController {

    public function online(){
        $this->cache(false);
        $this->notice = "ÔÚÏßºÃÓÑ";
        App::import('Sanitize');
        $u = User::getInstance();
        $online = $u->getOnlineFriends();
        if(count($online) > 0){
            foreach($online as $v){
                $info[] = array(
                    "fid" => $v->userid,
                    "from" => $v->userfrom,
                    "mode" => $v->mode,
                    "idle" => sprintf('%02d:%02d',intval($v->idle/60), ($v->idle%60))
                );
            }
            $this->set("friends", $info);
        }
    }
}
?>
