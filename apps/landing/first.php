<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Landing;
$Layout->init(JFStudio\Router::getName($route),'first',"simple",'',TO_ROOT.'/');

$Layout->setScriptPath(HCStudio\Connection::getMainPath() . '/src/');
$Layout->setScript([
	'theme.min.css',
	'jquery.mask.js',
	'landing2.css',
	'landing2.vue.js',
]);

$Layout->setVar([
]);
$Layout();