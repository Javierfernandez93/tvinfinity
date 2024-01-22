<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true) {
	HCStudio\Util::redirectTo(TO_ROOT . "/apps/backoffice/");
}
 
$Layout = JFStudio\Layout::getInstance();
$Layout->init("Únete a Cronos","index","index","",TO_ROOT."/");

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['signupMedical.css','signup-admin.js'],true);

$Layout->setVar([
	"nav"=>'medical',
	"black_theme"=>true,
	'UserLogin' => $UserLogin,
	'sponsor_id' => $sponsor_id,
	'Country' => (new World\Country)
]);

$Layout();