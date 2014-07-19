<?php
class friendController extends NF_MobileController {

    public function onlineAction(){
        $this->requestLogin();
        $this->notice = "ÔÚÏßºÃÓÑ";
        load('model/friend');
        $online = Friend::getOnlineFriends();
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
