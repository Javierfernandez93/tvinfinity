<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if(true)
{	
    if($data['whatsapp_list_per_user_id'])
    {
        if($list = (new MoneyTv\WhatsAppListPerUser)->get($data['whatsapp_list_per_user_id']))
        {
            $data['list'] = $list;
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_LISTS';
            $data['s'] = 1;
        }
    } else {
        $data['r'] = 'NOT_WHATSAPP_LIST_PER_USER_ID';
	    $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 