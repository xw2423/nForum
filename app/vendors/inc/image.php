<?php
class Image{

    private $_source = '';
    private $_stream = false;
    private $_info = null;
    private $_file = null;
    private $_im = null;


    public function __construct($source, $stream = false){
        if(false !== $stream){
            $this->_source = $source;
            $this->_stream = true;
        }else if(file_exists($source))
            $this->_source = $source;
        else
            throw new ImageNullException();
    }

    public function __destruct(){
        if(null !== $this->_im)
            ImageDestroy($this->_im);
        if(false !== $this->_stream && null !== $this->_file)
            @unlink($this->_file);
    }

    public function getWidth(){
        if(false !== $this->_stream)
            return imagesx($this->getIm());
        $info = $this->getInfo();
        return $info[0];
    }

    public function getHeight(){
        if(false !== $this->_stream)
            return imagesy($this->getIm());
        $info = $this->getInfo();
        return $info[1];
    }

    //1:gif 2:jpg 3:png
    public function getFormat(){
        $info = $this->getInfo();
        return $info[2];
    }

    public function scale($dest, $width = null, $height = null, $force = false){
        $im = $this->getIm();
        if(null === $width && null === $height){
            $w = $width = imagesx($im);
            $h = $height = imagesy($im);
        }else if(false === $force){
            $w = imagesx($im);
            $h = imagesy($im);
            if(null === $width)
                $width = round($w * $height / $h);
            else if(null === $height)
                $height = round($h * $width / $w);
            else{
                if($w / $h > $width / $height)
                    $height = round($h * $width / $w);
                else
                    $width = round($w * $height / $h);
            }
        }

        if(function_exists("imagecreatetruecolor")
            && ($ni = imagecreatetruecolor($width, $height))){
            imagecopyresampled($ni, $im, 0, 0, 0, 0, $width, $height, $w, $h);
        }else{
            $ni = imagecreate($width, $height);
            imagecopyresized($ni, $im, 0, 0, 0, 0, $width, $height, $w, $h);
        }
        imagejpeg($ni, $dest);
        ImageDestroy($ni);
    }

    public function thumbnail($dest, $width = null, $height = null, $force = false){
        $w = $this->getWidth();
        $h = $this->getHeight();
        if(null !== $width && $width >= $w)
            $width = $w;
        if(null !== $height && $height >= $h)
            $height = $h;

        $this->scale($dest, $width, $height, $force);
    }

    public function exif(){
        if(false === exif_read_data($this->getFile(),"IFD0"))
            return false;
        return exif_read_data($this->getFile(), 0, true);
    }

    public function getInfo(){
        if(null === $this->_info){
            $inf = @getimagesize($this->getFile());
            if($inf === false)
                throw new ImageNullException();
            $this->_info = $inf;
        }
        return $this->_info;
    }

    public function getFile(){
        if(null === $this->_file){
            if(false !== $this->_stream){
                $this->_file = tempnam(CACHE, "image_");
                file_put_contents($this->_file, $this->_source);
            }else
                $this->_file  = $this->_source;
        }
        return $this->_file;
    }

    public function getIm(){
        if(null === $this->_im){
            if(false !== $this->_stream){
                $im = imagecreatefromstring($this->_source);
                if(false === $im)
                    throw new ImageNullException();
            }else{
                switch($this->getFormat()){
                    case 1:
                         $im = imagecreatefromgif($this->getFile());
                        break;
                    case 2:
                         $im = imagecreatefromjpeg($this->getFile());
                        break;
                    case 3:
                         $im = imagecreatefrompng($this->getFile());
                        break;
                    default:
                        throw new ImageNullException();
                }
            }
            $this->_im  = $im;
        }
        return $this->_im;
    }
}
class ImageNullException extends Exception{}
?>
