<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();
$Layout->init(" » Permisos requeridos","invalid_permission","admin","",TO_ROOT."/");

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['']);

$Layout->setVar([
	'UserSupport' => $UserSupport
]);
$Layout();