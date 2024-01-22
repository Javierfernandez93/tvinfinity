<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if(true)
{
    if($data['name'])
    {
        if($client = MoneyTv\Client::getClientByName($data['name']))
        {
            $data['client'] = $client;
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_FOUND";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_NAME";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_LOGGED";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 