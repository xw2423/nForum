<?php
class InstallShell extends NF_Shell{
    public function main($argc, $argv){
        $dir = array(TMP
            ,TMP . DS . 'compile'
            ,CACHE
            ,CACHE . DS . 'asset'
            ,CACHE . DS . 'nforum'
            ,CACHE . DS . 'thumbnail'
            ,WWW . DS . 'files/imgupload'
            ,WWW . DS . 'uploadFace'
        );

        foreach($dir as $v){
            self::line('install ' . $v . '......', false);
            if(!is_dir($v))
                mkdir($v, 0777, true);
            chmod($v, 0777);
            self::line('done');
        }
        self::line('install successfully!');
    }
}
