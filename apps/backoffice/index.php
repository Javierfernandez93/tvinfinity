<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === false) {
	HCStudio\Util::redirectTo(TO_ROOT."/apps/login/");
}

$UserLogin->isActiveOnPackage(1);
$UserLogin->checkRedirection();

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Backoffice;
$Layout->init(JFStudio\Router::getName($route),'index',"backoffice",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'backoffice.vue.js',
]);

$Layout->setVar([
	'route' =>  $route,
	'setApp' =>  true,
	'UserLogin' => $UserLogin
]);
$Layout();