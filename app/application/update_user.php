<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    if(updateUserLogin($data['user']))
    {
        if(updateUserData($data['user']))
        {
            if(updateUserContact($data['user']))
            {
                if(updateUserAccount($data['user']))
                {
                    if(updateUserAddress($data['user']))
                    {
                        if(updateUserReferral($data['user']))
                        {
                            $data["s"] = 1;
                            $data["r"] = "UPDATED_OK";
                        } else {
                            $data["s"] = 0;
                            $data["r"] = "NOT_UPDATED_USER_REFERRAL";
                        }  
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

function updateUserData($data = null) : bool
{
    $UserData = new Infinity\UserData;   
        
    if($UserData->cargarDonde("user_login_id = ?",$data['user_login_id']))
    {
        $UserData->names = $data['names'];
        
        return $UserData->save();
    }

    return false;
}

function updateUserContact($data = null) : bool
{
    $UserContact = new Infinity\UserContact;   
        
    if($UserContact->cargarDonde("user_login_id = ?",$data['user_login_id']))
    {
        $UserContact->phone = $data['phone'];

        return $UserContact->save();    
    }

    return false;
}


function updateUserAccount($data = null) : bool
{
    $UserAccount = new Infinity\UserAccount;   
        
    if($UserAccount->cargarDonde("user_login_id = ?",$data['user_login_id']))
    {
        $UserAccount->referral_notification = filter_var($data['referral_notification'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $UserAccount->referral_email = filter_var($data['referral_email'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $UserAccount->info_email = filter_var($data['info_email'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        
        return $UserAccount->save();
    }

    return false;
}

function updateUserAddress($data = null) : bool
{
    $UserAddress = new Infinity\UserAddress;   
        
    if($UserAddress->cargarDonde("user_login_id = ?",$data['user_login_id']))
    {
        $UserAddress->country_id = $data['country_id'];
        
        return $UserAddress->save();
    }

    return false;
}

function updateUserLogin($data = null) : bool
{
    $UserLogin = new Infinity\UserLogin(false,false);   
        
    if($UserLogin->cargarDonde("user_login_id = ?",$data['user_login_id']))
    {
        $UserLogin->email = $data['email'];
        $UserLogin->password = $data['password'] ? sha1($data['password']) : $UserLogin->password;
        $UserLogin->signup_date = $data['signup_date'] ? strtotime($data['signup_date']) : $UserLogin->signup_date;
        
        return $UserLogin->save();
    }

    return false;
}

function updateUserReferral($data = null) : bool
{
    $UserReferral = new Infinity\UserReferral;   
        
    if($UserReferral->cargarDonde("user_login_id = ?",$data['user_login_id']))
    {
        $UserReferral->referral_id = $data['referral']['user_login_id'];
        
        return $UserReferral->save();
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 