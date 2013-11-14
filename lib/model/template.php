<?php
load('model/overload');
/**
 * class Template
 * array
     _info => array
         'TITLE' => string 'title' (length=5)
         'TITLE_TMPL' => string '' (length=0)
         'CONT_NUM' => int 2
         'FILENAME' => string '' (length=0)
         'NUM' => int
     q1 => array
         'TEXT' => string '111111' (length=6)
         'LENGTH' => int 11
     q2 => array
         'TEXT' => string '222' (length=3)
         'LENGTH' => int 22

 * @extends xw
 * @implements xw
 * @author xw
 */
class Template extends OverloadObject{

    protected $_que = array();
    private $_board;
    private $_num;

    public static function getTemplates($board){

        $res = array();
        $num = bbs_get_tmpls($board->NAME, $res);
        if($num <= 0)
            $res = array();
        else
            foreach($res as $k=>&$v){
                $v = new Template($v, $k+1, $board);
            }
        load('inc/pagination');
        return new ArrayPageableAdapter($res);
    }

    public static function getInstance($num, $board){
        $res = array();
        $retnum = bbs_get_tmpl_from_num($board->NAME,$num,$res);
        if($retnum <= 0 || $res[0]["CONT_NUM"] == 0)
            throw new TemplateNullException();
        $info = array_shift($res);
        return new Template($info, $num, $board, $res);
    }

    public function getQ($num){
        if(!isset($this->_que[$num]))
            throw new TemplateQNullException();
        return $this->_que[$num];
    }

    public function getPreview($val){
        $tmp = "tmp/" . User::getInstance()->userid . ".tmpl.tmp" ;
        $title = bbs_make_tmpl_file($this->_board->NAME, $this->NUM, @$val[0], @$val[1], @$val[2], @$val[3], @$val[4], @$val[5], @$val[6], @$val[7], @$val[8], @$val[9], @$val[10], @$val[11], @$val[12], @$val[13], @$val[14], @$val[15], @$val[16], @$val[17], @$val[18], @$val[19], @$val[20]);
        return array($title, bbs_printansifile_noatt($tmp), file_get_contents($tmp));
    }

    private function __construct($info, $num, $board, $que = array()){
        $info['NUM'] = $num;
        $this->_info = $info;
        $this->_board = $board;
        $this->_que = $que;
    }
}
class TemplateNullException extends Exception {}
class TemplateQNullException extends Exception {}
