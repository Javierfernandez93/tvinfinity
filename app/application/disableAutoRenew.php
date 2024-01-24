<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{
    if($data['client_id'])
    {
        if(Infinity\ServicePerClient::disableAutoRenew($data['client_id']))
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_DISABLED";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_CLIENT_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}
echo json_encode($data);