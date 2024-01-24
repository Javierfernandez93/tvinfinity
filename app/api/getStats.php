<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{
    $Client = new Infinity\Client;

    $data['stats'] = [
        "active_accounts" => $Client->getActiveCounts($UserLogin->company_id),
        "total_demos" => $Client->getDemoCount($UserLogin->company_id),
        "total_services" => $Client->getServiceCount($UserLogin->company_id),
        "credits" => $UserLogin->getCredits()
    ];

    $data["s"] = 1;
    $data["r"] = "DATA_OK";
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_LOGGED";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 