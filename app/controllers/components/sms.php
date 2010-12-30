<?php
/**
 * sms component for nforum 
 * @author xw       
 */
App::import("vendor", "inc/sms");
class SmsComponent extends Object {    

    public $components = array("ByrSession");
    public function initialize(&$controller) {
        $this->controller = $controller;
    }

    public function send($num, $content){
        if(!preg_match("/^1[0-9]{10}$/", $num))
            return false;
        $time = date("Y-m-d H:i:s", time());
        $ip = $this->ByrSession->from;
        $sender = SMS::getInstance($num);
        $ret = $sender->send($num, $content, $time);
        $mark = $sender->getMark();
        $val = array('k'=>array('num', 'content', 'time', 'ip', 'status', 'mark'), 'v'=>array(array($num, $content, $time, $ip, $ret?1:0, $mark)));
        $db = DB::getInstance();
        $db->insert('log_sms', $val);
        if(false === $ret){
            App::import("vendor", "model/article");
            $board = Configure::read("phone.board");
            $title = $num;
            $content = <<<EOT
phone: {$num}
ip: {$ip}
status: fail
sender: {$mark}
time: {$time}
EOT;
            Article::autoPost($board, $title, $content);
        }
        return $ret;
    }
}
?>
