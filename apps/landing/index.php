<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$landing = HCStudio\Util::getVarFromPGS('landing');

if($landing)
{
	if($user_login_id = (new Infinity\UserAccount)->getIdByLanding($landing))
	{
		HCStudio\Util::redirectTo(Infinity\UserLogin::_getLanding($user_login_id));
	}

	d($landing);
}


$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Landing;
$Layout->init(JFStudio\Router::getName($route),'index',"simple",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'theme.min.css',
	'landing.css',
	'landing.vue.js',
]);

$Layout->setVar([
]);
$Layout();