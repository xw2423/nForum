<?php
error_reporting(E_ALL ^ (E_DEPRECATED | E_USER_DEPRECATED));

if($argc < 2) exit();
array_shift($argv);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', array_shift($argv));
define('CONF', ROOT . DS . 'conf');
define('LIB', ROOT . DS . 'lib');
define('WWW', ROOT . DS . 'www');
define('CLI', 1);

require(LIB . DS . 'inc/func.php');

$app = c('application');
$app['directory'] = ROOT . DS . 'app';

define('APP', $app['directory']);
define('MODULE', APP . DS . 'modules');
define('TMP', APP . DS . 'tmp');
define('CACHE', TMP . DS . 'cache');
define('SHELL', APP . DS . 'shells');


if($argc == 2){
    NF_Shell::help();
}else{
    $app['modules'] = join(',', c('modules.install'));
    $app = new Yaf_Application(array('application' => $app));

    $arg = $shell = strtolower(array_shift($argv));
    if(false === ($m = strstr($shell, '.', true))){
        if(!load(SHELL . DS . $shell)){
            NF_Shell::help();
            exit();
        }
    }else{
        $shell = substr(strstr($shell, '.'), 1);
        if(!load(MODULE . DS . ucfirst($m) . DS . 'shells' . DS .$shell)){
            NF_Shell::help();
            exit();
        }
    }

    $class = ucfirst($shell) . 'Shell';
    if(!class_exists($class) || !is_subclass_of($class, 'NF_Shell')){
        NF_Shell::help();
        exit();
    }

    NF_Shell::line("nForum Shell");
    NF_Shell::line("\nexecute: $arg\n");
    $app->execute(array(new $class, 'main'), $argc - 2, $argv);
}

abstract class NF_Shell{
    abstract public function main($argc, $argv);

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
