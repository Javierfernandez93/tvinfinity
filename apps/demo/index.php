<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::IptvDemo;
$Layout->init(JFStudio\Router::getName($route),'index',"blank",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'jquery.mask.js',
	'demo.vue.js',
]);

$Layout->setVar([
	
]);
$Layout();