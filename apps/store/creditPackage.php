<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new Infinity\UserLogin;

$route = JFStudio\Router::StoreCredit;

if($UserLogin->logged === false) {
	Infinity\UserLogin::redirectTo(JFStudio\Router::getName($route));
}

$UserLogin->checkRedirection();

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::StoreCredit;
$Layout->init(JFStudio\Router::getName($route),'creditPackage',"backoffice",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'store.css',
	'storecreditpackage.vue.js',
]);

$Layout->setVar([
	'route' =>  $route,
	'setApp' =>  true,
	'UserLogin' => $UserLogin
]);
$Layout();