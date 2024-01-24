<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

$Layout = JFStudio\Layout::getInstance();
$Layout->init((new Infinity\Landing)->getLandingTitleByPath($data['path']),'landings',"simple",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'theme.min.css',
	'landings.css',
	'landings.vue.js',
]);

$Layout->setVar([
]);
$Layout();