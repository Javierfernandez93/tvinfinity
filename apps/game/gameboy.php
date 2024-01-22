<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === false) {
	HCStudio\Util::redirectTo(TO_ROOT."/apps/login/");
}

if(!(new MoneyTv\BuyPerUser)->hasPackageBuy($UserLogin->company_id,9)) 
{
	HCStudio\Util::redirectTo(TO_ROOT."/apps/backoffice/");
}

$UserLogin->checkRedirection();

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::GameGuide;
$Layout->init(JFStudio\Router::getName($route),'gameboy',"backoffice",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'gameboy.vue.js',
]);

$Layout->setVar([
	'route' =>  $route,
	'setApp' =>  true,
	'UserLogin' => $UserLogin
]);
$Layout();