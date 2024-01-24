<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$key_name = HCStudio\Util::getVarFromPGS("key_name");

$Layout = JFStudio\Layout::getInstance();
$Layout->init("Cursos","view","backoffice","",TO_ROOT."/");

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['courses.*','view-courses.css']);

$UserLogin = new Infinity\UserLogin;
$Course = new OwnBoss\Course;

$Course->cargarDonde("course_id = ?",HCStudio\Util::getVarFromPGS("cid"));

$Layout->setVar([
	"nav" => "courses",
	"static_sidebar" => true,
	"Course" => $Course,
	"UserLogin" => $UserLogin,
]);
$Layout();