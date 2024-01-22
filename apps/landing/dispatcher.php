<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

if(isset($_GET['path']))
{
    if(isset($_GET['landing']))
    {
        HCStudio\Util::redirectTo(HCStudio\Connection::getMainPath()."/apps/landing/landings?path={$_GET['path']}&landing={$_GET['landing']}");
    }
}