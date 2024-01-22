<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserSupport = new MoneyTv\UserSupport;

if($UserSupport->_loaded === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

if($UserSupport->hasPermission('list_notices') === false) {
	HCStudio\Util::redirectTo('../../apps/admin/invalid_permission');
}

$route = JFStudio\Router::AdminLanding;
$Layout = JFStudio\Layout::getInstance();
$Layout->init(JFStudio\Router::getName($route),"index","admin","",TO_ROOT."/");

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'adminlanding.vue.js'
]);

$Layout->setVar([
	'route' => $route,
	'UserSupport' => $UserSupport
]);
$Layout();