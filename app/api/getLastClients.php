<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true)
{
    if($clients = (new MoneyTv\Client)->findAll("user_login_id = ?",$UserLogin->company_id,null,null,5))
    {
        $data["clients"] = MoneyTv\Client::formatClients($clients);
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