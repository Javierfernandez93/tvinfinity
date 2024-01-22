<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

if($data['email'])
{
    $UserLogin = new MoneyTv\UserLogin;

    if($UserLogin->isUniqueMail($data['email']))
    {
        $data['encrypted'] = true;
        
        if($user_login_id = $UserLogin->doSignup($data))
        {
            if($UserLogin->login($data['email'],$data['password']))
            {
                $data['s'] = 1;
                $data['r'] = 'LOGGED_OK';
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_LOGGED';
            }
        } else {
            $data['s'] = 0;
            $data['r'] = 'ERROR_ON_SIGNUP';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'MAIL_ALREADY_EXISTS';
    }
} else {
	$data['s'] = 0;
	$data['r'] = 'NOT_FIELD_SESSION_DATA';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 