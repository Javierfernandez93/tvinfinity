<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === false) {
	HCStudio\Util::redirectTo(TO_ROOT."/apps/login/");
}

$UserLogin->checkRedirection();

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::EditVCard;
$Layout->init(JFStudio\Router::getName($route),'edit',"backoffice",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'vcard.css',
	'editvcard.vue.js'
]);

$Layout->setVar([
	'route' =>  $route,
	'UserLogin' => $UserLogin
]);
$Layout();