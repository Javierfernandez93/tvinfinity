<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === false) {
	HCStudio\Util::redirectTo(TO_ROOT."/apps/login/");
}

// $s = MoneyTv\Lattv::call("user_info", [
// 	"username" => "javier",
// 	"password" => "1234"
// ]);

// d($s);

// $response = MoneyTv\ApiWhatsApp::sendWhatsAppMessage([
// 	'message' => MoneyTv\ApiWhatsAppMessages::getIptvSetUpDemoMessage(),
// 	'image' => null,
// 	'contact' => [
// 		"phone" => '5213317361196',
// 		"name" => 'javi',
// 		"user_name" => 'javier',
// 		"client_password" => '1234'
// 	]
// ]);

// d($response);

$UserLogin->checkRedirection();

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::IptvAddClient;
$Layout->init(JFStudio\Router::getName($route),'add',"backoffice",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'iptvadd.vue.js',
]);

$Layout->setVar([
	'route' =>  $route,
	'setApp' =>  true,
	'UserLogin' => $UserLogin
]);
$Layout();