<?php
/**
 * thumbnail component for nforum
 * @author xw
 */
class ThumbnailComponent extends Object {

    public function initialize(&$controller) {
        $this->controller = $controller;
    }

    //check and make thumbnail for archive, then output
    public function archive($type, $archive, $pos){
        $types = Configure::read('thumbnail');
        if(!in_array($type, array_keys($types)))
            $this->controller->_stop();

        $file = $archive->getFileName();
        $dest = $this->getArchiveThumbnail($type, $archive, $pos);

        if(!file_exists($dest) || filemtime($dest) < filemtime($file)){
            $atts = $archive->getAttList(false);
            $find = false;
            foreach($atts as $v){
                if(intval($pos) === $v['pos'] &&
                    in_array(strtolower(substr(strrchr($v['name'], "."), 1)), array('jpg', 'jpeg', 'png', 'gif')))
                    $find = true;
            }
            if(!$find)
                $this->controller->_stop();
            App::import('vendor', 'inc/image');
            ob_start();
            $archive->getAttach($pos);
            try{
                $img = new Image(ob_get_clean(), true);
                $img->thumbnail($dest, $types[$type][0], $types[$type][1]);
            }catch(ImageNullException $e){
                $this->controller->_stop();
            }
        }
        $this->controller->header('Content-Type: image/jpeg');
        echo file_get_contents($dest);
        $this->controller->_stop();
    }

    //check and make thumbnail for file, then output
    public function file($type, $src){
        $types = Configure::read('thumbnail');
        if(!in_array($type, array_keys($types)))
            $this->controller->_stop();

        $dest = $this->getFileThumbnail($type, $src);
        if(!file_exists($dest) || filemtime($dest) < filemtime($src)){
            App::import('vendor', 'inc/image');
            try{
                $img = new Image($src);
                $img->thumbnail($dest, $types[$type][0], $types[$type][1]);
            }catch(ImageNullException $e){
                $this->controller->_stop();
            }
        }
        $this->controller->header('Content-Type: image/jpeg');
        echo file_get_contents($dest);
        $this->controller->_stop();
    }

    public function getFileThumbnail($type, $src){
        return CACHE . 'thumbnail' . DS . $type . '_'. $this->getThumbnailName($src) . '.jpg';
    }

    public function getArchiveThumbnail($type, $archive, $pos){
        return $this->getFileThumbnail($type, $archive->getFileName() . '_' . $pos);
    }

    public function getThumbnailName($src){
        return substr(md5($src), 0, 24);
    }
}
