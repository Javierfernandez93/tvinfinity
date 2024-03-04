<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{
    if(Infinity\UserBank::editData($data))
    {
        $data["s"] = 1;
        $data["r"] = "LOGGED_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_EDIT";
    }    
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo HCStudio\Util::compressData($data); 