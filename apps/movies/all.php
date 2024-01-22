<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === false) {
	HCStudio\Util::redirectTo(TO_ROOT."/apps/login/");
}

$UserLogin->checkRedirection();

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Movies;
$Layout->init(JFStudio\Router::getName($route),'all',"movie",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'allmovies.vue.js',
	'all.css',
	'movie.css'
]);

$Layout->setVar([
	'route' =>  $route,
	'UserLogin' => $UserLogin
]);
$Layout();