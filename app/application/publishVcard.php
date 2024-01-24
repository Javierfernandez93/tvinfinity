<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['vcard_per_user_id'])
    {
        $VCardPerUser = new Infinity\VCardPerUser;
        
        if($VCardPerUser->loadWhere('vcard_per_user_id = ?',$data['vcard_per_user_id']))
        { 
            $data['status'] = Infinity\VCardPerUser::PUBLISHED;

            $VCardPerUser->status = $data['status'];
            
            if($VCardPerUser->save())
            {
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_SAVE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_VCARD_PER_USER';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_VCARD_PER_USER_ID';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 