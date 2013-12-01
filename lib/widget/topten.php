<?php
load("model/iwidget");

class toptenWidget extends WidgetAdapter{

    protected $_file;
    public function __construct(){
        $this->_file = BBS_HOME . '/xml/day.xml';
    }
    public function wGetTitle(){ return array("text"=>"十大热门话题", "url"=>""); }
    public function wGetTime(){
        if (!file_exists($this->_file)) {
            return time();
        }
        return filemtime($this->_file);
    }
    public function wGetList(){
        if (!file_exists($this->_file)) {
            return $this->_error('十大热门话题不存在');
        }
        $ret = array();
        $xml = simplexml_load_file($this->_file);
        if($xml === false)
            return $this->_error('十大热门话题不存在');
        foreach($xml->hotsubject as $v){
            $title = nforum_fix_gbk(urldecode($v->title));
            $board = ($v->o_board=="")?$v->board:$v->o_board;
            $id = ($v->o_groupid==0)?$v->groupid:$v->o_groupid;
            $ret[] = array("text" => $title . "(<span style=\"color:red\">" . $v->number . "</span>)", "url"=> "/article/" . rawurldecode($board) . "/" . $id);
        }
        if(empty($ret))
            return $this->_error('十大热门话题不存在');
        return array("s"=>parent::$S_LINE, "v"=>$ret);
    }
}
