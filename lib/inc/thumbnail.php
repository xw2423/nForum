<?php
/**
 * thumbnail for nforum
 * @author xw
 */
class Thumbnail{

    //check and make thumbnail for archive, then output
    public static function archive($type, $archive, $pos){
        $types = c('thumbnail');
        if(!in_array($type, array_keys($types)))
            nforum_error404(true);

        $file = $archive->getFileName();
        $dest = self::getArchiveThumbnail($type, $archive, $pos);

        if(!file_exists($dest) || filemtime($dest) < filemtime($file)){
            $atts = $archive->getAttList(false);
            $find = false;
            foreach($atts as $v){
                if(intval($pos) === $v['pos'] &&
                    in_array(strtolower(substr(strrchr($v['name'], "."), 1)), array('jpg', 'jpeg', 'png', 'gif')))
                    $find = true;
            }
            if(!$find)
                nforum_error404(true);
            load('inc/image');
            ob_start();
            $archive->getAttach($pos);
            try{
                $img = new Image(ob_get_clean(), true);
                $img->thumbnail($dest, $types[$type][0], $types[$type][1]);
            }catch(ImageNullException $e){
                exit();
            }
        }
        header('Content-Type: image/jpeg');
        echo file_get_contents($dest);
        exit();
    }

    //check and make thumbnail for file, then output
    public static function file($type, $src){
        $types = c('thumbnail');
        if(!in_array($type, array_keys($types)))
            nforum_error404(true);

        $dest = self::getFileThumbnail($type, $src);
        if(!file_exists($dest) || filemtime($dest) < filemtime($src)){
            load('inc/image');
            try{
                $img = new Image($src);
                $img->thumbnail($dest, $types[$type][0], $types[$type][1]);
            }catch(ImageNullException $e){
                nforum_error404(true);
            }
        }
        header('Content-Type: image/jpeg');
        echo file_get_contents($dest);
        exit();
    }

    public static function getFileThumbnail($type, $src){
        return CACHE . DS . 'thumbnail' . DS . $type . '_'. self::getThumbnailName($src) . '.jpg';
    }

    public static function getArchiveThumbnail($type, $archive, $pos){
        return self::getFileThumbnail($type, $archive->getFileName() . '_' . $pos);
    }

    public static function getThumbnailName($src){
        return substr(md5($src), 0, 24);
    }
}
