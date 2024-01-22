<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$Qr = new JFStudio\Qr; 

$Qr->url($_GET['url']);

$Qr->qrCode();