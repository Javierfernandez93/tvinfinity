<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserSupport = new MoneyTv\UserSupport;

if($UserSupport->_loaded === true) {
	HCStudio\Util::redirectTo('../../apps/admin/');
}

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::AdminLogin;
$Layout->init(JFStudio\Router::getName($route),"index","admin-login","",TO_ROOT."/");


$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'loginSupport.vue.js'
]);

// MoneyTv\WhatsAppSessionPerUser::generateApiKeys(2);
// MoneyTv\WhatsAppSessionPerUser::loadByAPIKeys([
// 	'client_id' => 'PtnoVFTI8WrcisXS',
// 	'client_secret' => 'VeFkw6fBjlux+flvjcXdFSr4zJ/pPf/yOMV+qPbOyfGKypxdzdcXZNqkJZihOTM2erTZ1vTZf7usbRiW5ybzLkyEQD6otbKvRLVSQesjGYjLgbVJo5abTUdixzEbW8QwYFByxb1xG2lXCEKIIc9xFBW0qRvjZyNk5pXNZpkLWQFba+qZ5hYtVi6N18CJtSOM:VEFMRU5UT1VNQlJFTExBMg==',
// ]);

$Layout->setVar("UserSupport",$UserSupport);
$Layout();