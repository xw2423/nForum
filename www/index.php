<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('CONF', ROOT . DS . 'conf');
define('LIB', ROOT . DS . 'lib');
define('WWW', ROOT . DS . 'www');
define('JS', WWW . DS . 'js');
define('CSS', WWW . DS . 'css');
define('IMAGE', WWW . DS . 'js');

require(LIB . DS . 'inc/func.php');

nforum_check_domain();

$app = c('application');
$app['directory'] = ROOT . DS . 'app';
$app['bootstrap'] = $app['directory'] . DS . 'boot.php';
$app['baseUri'] = c('site.base');
$app['modules'] = join(',', c('modules.install'));
$app['view']['ext'] = 'tpl';
$app['dispatcher']['catchException'] = true;
c('application', $app);

error_reporting(($app['debug'] ? E_ALL | E_STRICT: 0) & ~(E_DEPRECATED | E_USER_DEPRECATED));

$app  = new Yaf_Application(array('application' => $app));
$app->bootstrap()->run();
?>
