<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{
    $UserLogin->email = $data['email'];

    if($UserLogin->save())
    {
        if(updateUserData($data,$UserLogin->company_id))
        {
            if(updateUserContact($data,$UserLogin->company_id))
            {
                if(updateUserAccount($data,$UserLogin->company_id))
                {
                    if(updateUserAddress($data,$UserLogin->company_id))
                    {
                        if(updatePaymentMethodPerUser($data,$UserLogin->company_id))
                        {
                            $data["payment_method"] = true;
                        }
                        $data["s"] = 1;
                        $data["r"] = "UPDATED_OK";
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_UPDATED_USER_ADDRESS";
                    }  
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_UPDATED_USER_ACCOUNT";
                }            
            }  else {
                $data["s"] = 0;
                $data["r"] = "NOT_UPDATED_USER_CONTACT";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_UPDATED_USER_DATA";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_UPDATED_USER_LOGIN";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function updateUserData($data = null,$company_id = null)
{
    $UserData = new Infinity\UserData;   
        
    if($UserData->cargarDonde("user_login_id = ?",$company_id))
    {
        $UserData->names = $data['names'];
        
        return $UserData->save();
    }

    return false;
}

function updateUserContact($data = null,$company_id = null)
{
    $UserContact = new Infinity\UserContact;   
        
    if($UserContact->cargarDonde("user_login_id = ?",$company_id))
    {
        $UserContact->phone = $data['phone'];
        return $UserContact->save();    
    }

    return false;
}

function updateUserAccount($data = null,$company_id = null)
{
    $UserAccount = new Infinity\UserAccount;   
        
    if($UserAccount->cargarDonde("user_login_id = ?",$company_id))
    {
        $UserAccount->referral_notification = filter_var($data['referral_notification'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $UserAccount->referral_email = filter_var($data['referral_email'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        
        if(!$UserAccount->existLanding($company_id,$data['landing']))
        {
            $UserAccount->landing = $data['landing'];
        }
        $UserAccount->catalog_timezone_id = $data['catalog_timezone_id'];
        $UserAccount->info_email = filter_var($data['info_email'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        
        return $UserAccount->save();
    }

    return false;
}

function updateUserAddress($data = null,$company_id = null)
{
    $UserAddress = new Infinity\UserAddress;   
        
    if($UserAddress->cargarDonde("user_login_id = ?",$company_id))
    {
        $UserAddress->country_id = $data['country_id'];
        
        return $UserAddress->save();
    }

    return false;
}

function updatePaymentMethodPerUser($data = null,$company_id = null)
{
    $PaymentMethodPerUser = new Infinity\PaymentMethodPerUser;   
        
    if(!$PaymentMethodPerUser->cargarDonde("user_login_id = ?",$company_id))
    {
        $PaymentMethodPerUser->user_login_id = $company_id;
    }
    
    $PaymentMethodPerUser->bank = $data['bank'] ? $data['bank'] : $PaymentMethodPerUser->bank;
    $PaymentMethodPerUser->account = $data['account'] ? $data['account'] : $PaymentMethodPerUser->account;
    $PaymentMethodPerUser->clabe = $data['clabe'] ? $data['clabe'] : $PaymentMethodPerUser->clabe;
    $PaymentMethodPerUser->paypal = $data['paypal'] ? $data['paypal'] : $PaymentMethodPerUser->paypal;
        
    return $PaymentMethodPerUser->save();
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 