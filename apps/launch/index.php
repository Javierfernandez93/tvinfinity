<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Launch;
$Layout->init(JFStudio\Router::getName($route),'index',"simple",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'theme.min.css',
	'launch.css',
]);

$Layout->setVar([
]);
$Layout();