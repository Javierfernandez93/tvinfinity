<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    $filter = "AND user_login.catalog_campaing_id IN(".$UserSupport->campaing.")";

    if($users = $UserSupport->getUsers($filter))
    {
        $data["users"] = format($users);
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data['r'] = "DATA_ERROR";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function format(array $users = null) : array 
{
    $Country = new World\Country;
    $LicencePerUser = new Infinity\LicencePerUser;
    $CreditPerUser = new Infinity\CreditPerUser;
    
    return array_map(function($user) use($Country,$LicencePerUser,$CreditPerUser){
        $credits = $CreditPerUser->findField("user_login_id = ?",$user['user_login_id'],"credit");
        $user['credits'] = $credits ? $credits : 0;
        $user['active'] = Infinity\UserLogin::_isActive($user['company_id']);
        $user['countryData'] = $Country->getCountryNameAndPhoneArea($user['country_id']);

        return $user;
    },$users);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 