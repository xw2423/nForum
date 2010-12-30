<?php
App::import("vendor", "model/iwidget");
abstract class classicWidgetAdapter extends WidgetAdapter{
    
    protected $_file;
    public function wGetList(){}
    public function wGetTime(){
        if (!file_exists($this->_file)) {
            return time();
        }
        return filemtime($this->_file);
    }

    protected function parseXml($file){
        if (!file_exists($file)) {
            return $this->_error('该应用数据错误');
        }
        $ret = array();
        $xml = simplexml_load_file($file);
        if($xml === false)
            return $this->_error('该应用数据错误');

        foreach($xml->hotsubject as $v){
            $title = nforum_fix_gbk(urldecode($v->title));
            $board = ($v->o_board=="")?$v->board:$v->o_board;
            $id = ($v->o_groupid==0)?$v->groupid:$v->o_groupid;
            $ret[] = array("text" => $title, "url"=> "/article/" . $board . "/" . $id);
        }
        if(empty($ret))
            return $this->_error('该应用暂无数据');
        return array("s"=>parent::$S_LINE, "v"=>$ret);
    }
}

class recommendWidget extends classicWidgetAdapter{
    public function __construct(){
        $this->_file = BBS_HOME . '/xml/commend.xml';
    }
    public function wGetTitle(){ return array("text"=>"近期热点活动", "url"=>"/board/recommend"); }
    public function wGetList(){
        return $this->parseXml($this->_file);
    }
}

class blessWidget extends classicWidgetAdapter{
    public function __construct(){
        $this->_file = BBS_HOME . '/xml/bless.xml';
    }
    public function wGetTitle(){ return array("text"=>"十大祝福", "url"=>"/board/Blessing"); }
    public function wGetList(){
        return $this->parseXml($this->_file);
    }
}

?>
