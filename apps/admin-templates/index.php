<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();
$Layout->init(" Templates","index","admin","",TO_ROOT."/");

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['admin-templates.*']);

$Layout->setVar("UserSupport",$UserSupport);
$Layout();