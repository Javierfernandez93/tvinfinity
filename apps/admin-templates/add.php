<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();
$Layout->init(" Añadir template","add","admin","",TO_ROOT."/");

$UserSupport = new Zuum\UserSupport;

if($UserSupport->_loaded === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['tagify.*','admin-templates-add.*']);

$Layout->setVar("UserSupport",$UserSupport);
$Layout();