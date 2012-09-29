<?php
/**
 * auth img component using AuthImg
 *
 * @author xw
 */
class AuthImgComponent extends Object {

    public $components = array("Session");

    public function initialize(&$controller, $settings = array()) {
        $this->controller =& $controller;
    }

    public function getImage(){
        App::import('vendor', 'inc/authimg');
        $num = "0123456789";
        $op = "+-";
        $text = "";
        $text .= $num[mt_rand(0, 9)];
        $text .= $op[mt_rand(0, 1)];
        $text .= $num[mt_rand(0, 9)];
        $text .= '=';

        switch($text[1]){
            case '+':
                $res = intval($text[0]) + intval($text[2]);
                break;
            case '-':
                $res = intval($text[0]) - intval($text[2]);
                break;
        }

        $this->Session->activate();
        $this->Session->write("authNum",$res);
        $font =  APP . "vendors/inc/font/" . mt_rand(0, 10) . ".ttf";
        $img = new AuthImg();
        $img->setFormat('jpeg');
        $img->setWH(120, 40);
        $img->setFont($font);
        $img->setText($text);
        $img->getImg();
    }

    public function check($num){
        $this->Session->activate();
        $authNum = $this->Session->read('authNum');
        if(null === $authNum || intval($num) != $authNum)
            return false;
        return true;
    }

    public function destory(){
        $this->Session->activate();
        $this->Session->write("authNum",100);
    }
}
?>
