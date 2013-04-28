<?php
/* SVN FILE: $Id: routes.php 7945 2008-12-19 02:16:01Z gwoo $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.ctp)...
 */
    Router::parseExtensions('json', 'xml');

    $home = Configure::read('site.home');
    Router::connect('', array('controller' => 'forum', 'action' => 'front'));
    Router::connect('/', array('controller' => 'forum', 'action' => 'front'));
    Router::connect('/index', array('controller' => 'forum', 'action' => 'preIndex'));
    Router::connect($home, array('controller' => 'forum', 'action' => 'index'));
    Router::connect('/rss/board-:name', array('controller' => 'rss', 'action' => 'board'));
    Router::connect('/rss/topten', array('controller' => 'rss', 'action' => 'topten'));
    Router::connect('/rss/:file', array('controller' => 'rss', 'action' => 'classic'));
    Router::connect('/widget/set', array('controller' => 'widget', 'action' => 'ajax_set'));
    Router::connect('/widget/add', array('controller' => 'widget', 'action' => 'add'));
    Router::connect('/widget/list', array('controller' => 'widget', 'action' => 'ajax_list'));
    Router::connect('/widget/:name', array('controller' => 'widget', 'action' => 'ajax_widget'));
    Router::connect('/slist', array('controller' => 'section', 'action' => 'ajax_list'));
    Router::connect('/flist', array('controller' => 'favor', 'action' => 'ajax_list'));
    Router::connect('/board/:name/mode/:mode', array('controller' => 'board', 'action' => 'mode'), array('mode' => '\d+'));
    Router::connect('/board/:name/:action/:num', array('controller' => 'board', 'num' => null), array('num' => '\d+'));
    Router::connect('/article/:name/:gid', array('controller' => 'article', 'action' => 'index'), array('gid' => '\d+'));
    Router::connect('/article/:name/:action/:id', array('controller' => 'article', 'id' => null), array('id'=> '\d+'));
    Router::connect('/att/:name/:mode/:id/:pos/:type', array('controller' => 'attachment', 'action' => 'download', 'mode' => null, 'type' => null), array('id'=> '\d+', 'pos'=>'\d+', 'mode'=>'\d+', 'type'=>'[A-Za-z][\w-]*'));
    Router::connect('/att/:hash', array('controller' => 'attachment', 'action' => 'download'), array('hash'=> '[0-9A-Za-z+/ ]+\.[0-9A-Za-z]+'));
    Router::connect('/att/:name/:action/:id', array('controller' => 'attachment', 'id' => null), array('id'=> '\d+'));
    Router::connect('/refer/:type/:action', array('controller' => 'refer'));
    Router::connect('/section/:num', array('controller' => 'section', 'action' => 'index'));
    Router::connect('/user/query/:id', array('controller' => 'user', 'action' => 'ajax_query'));
    Router::connect('/online', array('controller' => 'forum', 'action' => 'online'));
    Router::connect('/mail/send', array('controller' => 'mail', 'action' => 'send'));
    Router::connect('/mail/ajax_preview', array('controller' => 'mail', 'action' => 'ajax_preview'));
    Router::connect('/mail/:type', array('controller' => 'mail', 'action' => 'index'));
    Router::connect('/mail/:type/:num', array('controller' => 'mail', 'action' => 'ajax_detail'), array('num' => '\d+'));
    Router::connect('/mail/:type/:action/:num', array('controller' => 'mail', 'num' => null), array('num'=> '\d+'));
    Router::connect('/fav', array('controller' => 'favor', 'action' => 'index'));
    Router::connect('/fav/op/:num', array('controller' => 'favor', 'action' => 'ajax_change'));
    Router::connect('/fav/:num', array('controller' => 'favor', 'action' => 'ajax_show'));
    Router::connect('/s/:action', array('controller' => 'search'));
    Router::connect('/authimg', array('controller' => 'reg', 'action' => 'authImg'));
    Router::connect('/flink', array('controller' => 'forum', 'action' => 'flink'));
    Router::connect('/adv/:type/set', array('controller' => 'adv', 'action' => 'advSet'));
    Router::connect('/adv/:type/del', array('controller' => 'adv', 'action' => 'advDel'));
    Router::connect('/adv/:type/add', array('controller' => 'adv', 'action' => 'advAdd'));
    Router::connect('/adv/:type', array('controller' => 'adv', 'action' => 'index'));

/********************
 * plugin vote
 *******************/
    $base = Configure::read('plugins.vote.base');
    Router::connect($base . '/:action/:vid', array('plugin'=>'vote', 'controller' => 'index'));

/********************
 * plugin mobile
 *******************/
    $base = Configure::read('plugins.mobile.base');
    Router::connect($base, array('controller' => 'index', 'plugin'=>'mobile'));
    Router::connect($base . '/hot/:t', array('controller' => 'index', 'plugin'=>'mobile', 'action' => 'hot'));
    Router::connect($base . '/go', array('controller' => 'index', 'plugin'=>'mobile', 'action' => 'searchBoard'));
    Router::connect($base . '/article/:name/:gid', array('controller' => 'article', 'plugin'=>'mobile'), array("gid"=>"\d+"));
    Router::connect($base . '/article/:name/:action/:gid/:mode', array('controller' => 'article', 'plugin'=>'mobile'));
    Router::connect($base . '/board/:name/:mode', array('controller' => 'board', 'plugin'=>'mobile'));
    Router::connect($base . '/section/:name', array('controller' => 'section', 'plugin'=>'mobile'));
    Router::connect($base . '/mail/send', array('controller' => 'mail', 'plugin'=>'mobile', 'action' => 'send'));
    Router::connect($base . '/mail/:type', array('controller' => 'mail', 'plugin'=>'mobile', 'action' => 'index'));
    Router::connect($base . '/mail/:type/:num', array('controller' => 'mail', 'plugin'=>'mobile', 'action' => 'show'), array('num' => '\d+'));
    Router::connect($base . '/mail/:type/:action/:num', array('controller' => 'mail', 'plugin'=>'mobile', 'num' => null), array('num'=> '\d+'));
    Router::connect($base . '/refer/:type/:action', array('controller' => 'refer', 'plugin'=>'mobile'));
    Router::connect($base . '/favor/:num', array('controller' => 'favor', 'plugin'=>'mobile', 'action' => 'index'), array("num"=>"\d+"));
    Router::connect($base . '/:controller/:action/*', array('plugin'=>'mobile'));

/********************
 * plugin api
 *******************/
    $base = Configure::read('plugins.api.base');
    Router::connect($base . '/article/:name/:action/:id', array('controller' => 'article', 'plugin'=>'api'), array("id"=>"\d+"));
    Router::connect($base . '/article/:name/:action', array('controller' => 'article', 'plugin'=>'api'));
    Router::connect($base . '/threads/:name/:id', array('controller' => 'article', 'action' => 'threads', 'plugin'=>'api'));
    Router::connect($base . '/user/login', array('controller' => 'user', 'action' => 'login', 'plugin'=>'api'));
    Router::connect($base . '/user/logout', array('controller' => 'user', 'action' => 'logout', 'plugin'=>'api'));
    Router::connect($base . '/user/:action/:id', array('controller' => 'user', 'plugin'=>'api'), array("id"=>"\w+"));
    Router::connect($base . '/board/:action/:name', array('controller' => 'board', 'plugin'=>'api'), array("name"=>"[-\w]+"));
    Router::connect($base . '/section', array('controller' => 'section', 'action' => 'root', 'plugin'=>'api'));
    Router::connect($base . '/section/:action/:name', array('controller' => 'section', 'plugin'=>'api'), array("name"=>"[-\w]+"));
    Router::connect($base . '/attachment/:name/:mode/:id/:pos/:type', array('controller' => 'attachment', 'plugin'=>'api', 'action'=>'download', 'mode' => null, 'type' => null), array('name'=>"\w+", 'id'=>"\d+", 'pos'=>'\d+', 'mode'=>'\d+', 'type'=>'\w[\w\d-]*'));
    Router::connect($base . '/attachment/:name/:action/:id', array('controller' => 'attachment', 'plugin'=>'api', 'id'=>null), array("id"=>"\d+"));
    Router::connect($base . '/refer/:type/:action/:index', array('controller' => 'refer', 'plugin'=>'api', "index" => null), array("index"=>"\d+"));
    Router::connect($base . '/mail/:type/:action/:num', array('controller' => 'mail', 'plugin'=>'api'), array("num"=>"\d+"));
    Router::connect($base . '/mail/send', array('controller' => 'mail', 'plugin'=>'api', 'action'=>'send'));
    Router::connect($base . '/mail/info', array('controller' => 'mail', 'plugin'=>'api', 'action'=>'info'));
    Router::connect($base . '/mail/:type', array('controller' => 'mail', 'action'=>'box', 'plugin'=>'api'));
    Router::connect($base . '/favorite/:action/:num', array('controller' => 'favorite', 'plugin'=>'api'), array("num"=>"\d+"));
    Router::connect($base . '/favorite/:action', array('controller' => 'favorite', 'plugin'=>'api'));
    Router::connect($base . '/search/:action', array('controller' => 'search', 'plugin'=>'api'));
    Router::connect($base . '/widget/:name', array('controller' => 'widget', 'plugin'=>'api'));
    Router::connect($base . '/*', array('controller' => 'ApiApp', 'action' => 'errorAPI', 'plugin'=>'api'));
?>
