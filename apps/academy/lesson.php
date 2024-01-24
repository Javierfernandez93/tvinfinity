<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new Infinity\UserLogin;

$route = JFStudio\Router::AcademyLesson;

if($UserLogin->logged === false) {
	Infinity\UserLogin::redirectTo(JFStudio\Router::getName($route));
}

$UserLogin->checkRedirection();

$Layout = JFStudio\Layout::getInstance();
$Layout->init(JFStudio\Router::getName($route),'lesson',"backoffice",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'lesson.css',
	'lesson.vue.js'
]);

$Layout->setVar([
	'route' =>  $route,
	'UserLogin' => $UserLogin
]);
$Layout();