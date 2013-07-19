<?php
/****************************************************
 * FileName: app/vendors/inc/authimg.php
 * Author: xw <wei.xiao.bupt@gmail.com>
 *****************************************************/
/**
 * class AuthImg
 * It is a simple class that change text to image
 * you can set image width,height,format,font and confusing rate
 *
 * @example
 * $img = new AuthImg();
 * $img->getImg();
 *
 * @author xw
 */
class AuthImg{

    /**
     * support image format
     *
     * @var string $PNG
     * @var string $JPEG
     * @var string $GIF
     * @acesss public
     * @static
     */
    public static $PNG = 'png';
    public static $JPEG = 'jpeg';
    public static $GIF = 'gif';

    /**
     * width and height(pixel)
     *
     * @var int $_width
     * @var int $_height
     * @acesss private
     */
    private $_width = 70;
    private $_height = 25;

    /**
     * text to change
     *
     * @var string $_text
     * @acesss private
     */
    private $_text = '0000';

    /**
     * the ttf file that be used to create text
     *
     * @var mixed $_font boolean|string
     * @acesss private
     */
    private $_font = false;

    /**
     * confusing point rate
     * there is $_confuseRate * 15 confusing point in every 100 pixel
     * it should more than 0 and less than 0.8
     *
     * @var float $_confuseRate
     * @acesss private
     */
    private $_confuseRate = 0.5;

    private $_img = null;
    private $_format = 'png';

    public function __destruct(){
        if(is_resource($this->_img))
            imagedestroy($this->_img);
    }

    public function setFormat($f){
        //use reflection to valid value
        $ref = new ReflectionClass(__CLASS__);
        if(in_array($f, $ref->getStaticProperties())){
            $this->_format = $f;
        }
    }

    public function setWH($w, $h){
        $this->_width = (int)$w;
        $this->_height = (int)$h;
    }

    public function setText($t){
        $this->_text = (string)$t;
    }

    public function setFont($ttf){
        if(file_exists($ttf))
            $this->_font = (string)$ttf;
    }

    public function setConfuse($rate){
        if($rate < 0)
            $rate = 0;
        else if($rate > 0.8)
            $rate = 0.8;
        $this->_confuseRate = $rate;
    }

    public function getImg(){
        $this->_header();
        $this->_init();
        $this->_confuse();
        $this->_create();
        $func = "image" . $this->_format;
        $func($this->_img);
        imagedestroy($this->_img);
    }

    private function _header(){
        header('Content-type: image/' . $this->_format);
    }

    private function _init(){
        $this->_img = imagecreate($this->_width, $this->_height);
        if(false === $this->_img)
            throw new AuthImgException('img null');
        imagecolorallocate($this->_img, mt_rand(220,255), mt_rand(220,255), mt_rand(220,255));
    }

    private function _confuse(){
        $cNum = round($this->_confuseRate * 256);
        $loop = round($this->_width * $this->_height * 0.15 * $this->_confuseRate / $cNum);
        for($i=0; $i<=$cNum - 1; $i++){
            $pixel = imagecolorallocate($this->_img, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
            $j = $loop;
            while(($j--) > 0)
                imagesetpixel($this->_img, mt_rand(0, $this->_width), mt_rand(0, $this->_height),$pixel);
        }
    }

    private function _create(){
        $len = strlen($this->_text);
        if($len == 0)
            return;

        if(false === $this->_font){
            //if no ttf file, use font in gd
            $size = 5;
            $fh = imagefontheight($size);
            $fw = imagefontwidth($size);
        }else{
            $fh = round($this->_height * 2 / 3);
            $size = round($fh * 72 / 96);
            $fw = round($fh * 3 / 5);
        }
        $xgap = round(($this->_width - $len * $fw) / ($len + 1));
        $ygap = round(($this->_height - $fh) / 2);
        for($i = 0; $i <= $len - 1; $i++){
            $color = imagecolorallocate($this->_img, mt_rand(0,150), mt_rand(0,120), mt_rand(0,220));
            $x = $i * ($fw + $xgap) + round($xgap * 1.5) - mt_rand(0, $xgap);
            $y = $ygap + round($ygap / 2) - mt_rand(0, $ygap);
            if(false === $this->_font){
                imagechar($this->_img, $size, $x, $y, $this->_text[$i], $color);
            }else{
                $y += $fh;
                imagettftext($this->_img, mt_rand($size - 1, $size + 1), mt_rand(0, 30) - 15, $x, $y, $color, $this->_font, $this->_text[$i]);
            }
        }
    }

}
class AuthImgException extends Exception{}
?>
