<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Landing2;
$Layout->init(JFStudio\Router::getName($route),'second',"simple",'',TO_ROOT.'/');

$Layout->setScriptPath(HCStudio\Connection::getMainPath() . '/src/');
$Layout->setScript([
	'scroll-entrance.js',
	'theme.min.css',
	'landing3.css',
	'landing3.vue.js',
]);

$Layout->setVar([
]);
$Layout();