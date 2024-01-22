<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$ServicePerClient = new MoneyTv\ServicePerClient;

// if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->_loaded === true)
if(true)
{
    $users = (new MoneyTv\BuyPerUser)->getAllUsersWithBuy();

    if(isset($users) && is_array($users))
    {
        $UserLogin = new MoneyTv\UserLogin(false,false);

        foreach($users as $user)
        {
            if(!$UserLogin->_isActiveOnPackage(1,$user['user_login_id'],3))
            {
                $UserLogin->_disableAccount($UserLogin->company_id);
            }
        }
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 