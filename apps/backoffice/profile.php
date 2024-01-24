<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === false) {
	HCStudio\Util::redirectTo(TO_ROOT."/apps/login/");
}

$UserLogin->checkRedirection();

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Profile;
$Layout->init(JFStudio\Router::getName($route),'profile',"backoffice",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'jquery.mask.js',
	'profile.css',
	'profile.vue.js'
]);

$Layout->setVar([
	'route' =>  $route,
	'floating_nav' =>  true,
	'UserLogin' => $UserLogin
]);
$Layout();