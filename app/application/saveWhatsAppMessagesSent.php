<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if(true) 
{			
    $data['user_login_id'] = (new Infinity\WhatsAppSessionPerUser)->getUserBySessionName($data['sessionName']);

    if($data['contacts'])
    {
        if(Infinity\WhatsAppMessageSendPerContact::saveMessages([
            'contacts' => $data['contacts'],
            'user_login_id' => $data['user_login_id'],
            'message' => $data['message']
        ]))
        {
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_SAVE';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_CONTACTS';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 