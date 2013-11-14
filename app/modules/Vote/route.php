<?php
$base = c('modules.vote.base');
$export[] = array($base . '/:action/:vid', array('module' => 'vote', 'controller' => 'index', 'action' => null, 'vid' => null), array('vid' => '[\d]+'));
