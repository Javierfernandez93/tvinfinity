<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new MoneyTv\UserLogin;

$route = JFStudio\Router::Academy;

if($UserLogin->logged === false) {
	MoneyTv\UserLogin::redirectTo(JFStudio\Router::getName($route));
}

$UserLogin->checkRedirection();

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Academy;
$Layout->init(JFStudio\Router::getName($route),'index',"backoffice",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'academy.css',
	'academy.vue.js'
]);

$Layout->setVar([
	'route' =>  $route,
	'UserLogin' => $UserLogin
]);
$Layout();