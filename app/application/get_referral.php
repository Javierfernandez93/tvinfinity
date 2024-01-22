<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;
$UserSupport = new MoneyTv\UserSupport;

if($UserLogin->logged === false || $UserSupport->_loaded === true)
{
    if($data['user_login_id'])
    {
        $UserData = new MoneyTv\UserData;
        
        if($referral = $UserLogin->getProfile($data['user_login_id']))
        {
            $data['referral'] = $referral;
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_DATA";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_LOGIN_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 