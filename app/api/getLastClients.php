<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{
    if($clients = (new Infinity\Client)->findAll("user_login_id = ?",$UserLogin->company_id,null,null,5))
    {
        $data["clients"] = Infinity\Client::formatClients($clients);
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
	    $data["r"] = "NOT_CLIENTS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_LOGGED";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 