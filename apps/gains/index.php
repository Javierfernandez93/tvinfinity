<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === false) {
	HCStudio\Util::redirectTo(TO_ROOT."/apps/login/");
}
// $UserWallet = new MoneyTv\UserWallet;
// $UserWallet->getSafeWallet($UserLogin->company_id);

// $ammount = $UserWallet->getBalance(" AND transaction_per_wallet.transaction_id IN ('".MoneyTv\Transaction::INVESTMENT."','".MoneyTv\Transaction::REFERRAL_INVESTMENT."')");
// $UserWallet->depositGains($ammount);
// // echo $UserWallet->getBalance(" AND transaction_per_wallet.transaction_id IN ('".MoneyTv\Transaction::INVESTMENT."','".MoneyTv\Transaction::REFERRAL_INVESTMENT."')");
// die;
$UserLogin->checkRedirection();

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Gains;
$Layout->init(JFStudio\Router::getName($route),'index',"backoffice",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'gains.vue.js'
]);

$Layout->setVar([
	'route' =>  $route,
	'UserLogin' => $UserLogin
]);
$Layout();