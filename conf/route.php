<?php
/** add route here */

$export[] = array('/', array('controller' => 'forum', 'action' => 'front'));
$export[] = array(c('site.home'), array('controller' => 'forum', 'action' => 'index'));
$export[] = array('/index', array('controller' => 'forum', 'action' => 'pre'));
$export[] = array('/section/ajax_list', array('controller' => 'section', 'action' => 'ajax_list'));
$export[] = array('/board/:name/:action/:num', array('controller' => 'board', 'action' => null, 'num' => null), array('num' => '\d+'));
$export[] = array('/article/:name/:action/:id', array('controller' => 'article', 'action' => null, 'id' => null), array('id' => '\d+'));
$export[] = array('/att/:name/:mode/:id/:pos/:type', array('controller' => 'attachment', 'action' => 'download', 'mode' => null, 'type' => null), array('id' => '\d+', 'pos' => '\d+', 'mode' => '\d+', 'type' => '[A-Za-z][\w-]*'));
$export[] = array('/att/:name/:action/:id', array('controller' => 'attachment', 'id' => null), array('action' => '[a-z_]+', 'id'=> '\d+'));
$export[] = array('/att/:hash', array('controller' => 'attachment', 'action' => 'download'));
$export[] = array('/refer/:type/:action', array('controller' => 'refer', 'action' => null));
$export[] = array('/user/query/:id', array('controller' => 'user', 'action' => 'ajax_query'));
$export[] = array('/section/:num', array('controller' => 'section', 'action' => 'index'));
$export[] = array('/mail/send', array('controller' => 'mail', 'action' => 'send'));
$export[] = array('/mail/ajax_preview', array('controller' => 'mail', 'action' => 'ajax_preview'));
$export[] = array('/mail/:type/:action/:num', array('controller' => 'mail', 'action' => null, 'num' => null), array('num'=> '\d+'));
$export[] = array('/s', array('controller' => 'search', 'action' => 'index'));
$export[] = array('/s/:action', array('controller' => 'search'));
$export[] = array('/authimg', array('controller' => 'reg', 'action' => 'authimg'));
$export[] = array('/widget/set', array('controller' => 'widget', 'action' => 'ajax_set'));
$export[] = array('/widget/add', array('controller' => 'widget', 'action' => 'add'));
$export[] = array('/widget/list', array('controller' => 'widget', 'action' => 'ajax_list'));
$export[] = array('/widget/:name', array('controller' => 'widget', 'action' => 'ajax_widget'));
$export[] = array('/rss/topten', array('controller' => 'rss', 'action' => 'topten'));
$export[] = array('/rss/:board', array('controller' => 'rss', 'action' => 'board'), array('board' => 'board-\w+'));
$export[] = array('/rss/:file', array('controller' => 'rss', 'action' => 'classic'));
$export[] = array('/fav/op/:num', array('controller' => 'favor', 'action' => 'ajax_change'));
$export[] = array('/fav/:num', array('controller' => 'favor', 'action' => 'ajax_show'));
$export[] = array('/fav', array('controller' => 'favor', 'action' => 'index'));
$export[] = array('/adv/:action/:type', array('controller' => 'adv', 'action' => null), array('type' => '\d'));
