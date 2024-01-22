<?php

use GPBMetadata\Google\Type\Money;

 define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true)
{   
    if($landings = (new MoneyTv\Landing)->getAll())
    {
        $data['hasLandingConfigurated'] = $UserLogin->_data['user_account']['landing'] ? true : false;
        $data['userLanding'] = (new MoneyTv\UserAccount)->getLandingById($UserLogin->company_id);
        $data['landings'] = $landings;
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_LANDINGS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 