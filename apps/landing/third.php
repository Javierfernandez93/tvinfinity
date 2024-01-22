<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Landing2;
$Layout->init(JFStudio\Router::getName($route),'third',"simple",'',TO_ROOT.'/');

$Layout->setScriptPath(HCStudio\Connection::getMainPath() . '/src/');
$Layout->setScript([
	'theme.min.css',
	'landing4.css',
	'countdown.js',
	'landing4.vue.js',
]);

$Layout->setVar([
]);
$Layout();