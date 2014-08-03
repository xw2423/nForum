<?php
abstract class NF_Shell{
    abstract public function main($argc, $argv);

    protected function _initKbs($sysop = false){
        if (!bbs_ext_initialized())
            bbs_init_ext();
        if($sysop)
            bbs_setSYSOP_nologin();
        else
            bbs_setguest_nologin();
    }

    public static function help(){
        self::line("nForum Shell");
        self::line("\nModule Index:");
        self::line("  Path: " . SHELL);
        self::line("  Shells:");

        $shells = scandir(SHELL);

        foreach($shells as $v){
            if('.' === $v || '..' === $v)
                continue;
            load(SHELL . DS . $v);
            $v = strstr($v, '.', true);
            $class = ucfirst(strtolower($v)) . 'Shell';
            if(class_exists($class) && is_subclass_of($class, 'NF_Shell')){
                self::line('    ' . $v);
            }
        }

        foreach(c('modules.install') as $m){
            if('index' === $m) continue;
            $m_shell = MODULE . DS . ucfirst($m) . DS . 'shells';
            if(is_dir($m_shell)){
                self::line("\nModule " . ucfirst($m) . ":");
                self::line("  Path: " . $m_shell);
                self::line("  Shells:");
                foreach(scandir($m_shell) as $v){
                    if('.' === $v || '..' === $v)
                        continue;
                    load($m_shell . DS . $v);
                    $v = strstr($v, '.', true);
                    $class = ucfirst(strtolower($v)) . 'Shell';
                    if(class_exists($class) && is_subclass_of($class, 'NF_Shell')){
                        self::line('    ' . $m . '.' . $v);
                    }
                }
            }
        }

        self::line('');
    }

    public static function line($str, $br = true){
        echo $str;
        if($br) echo "\n";
    }
}
