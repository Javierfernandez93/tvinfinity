<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true) 
{	
    if($data['sessionName'])
    {
        if(Infinity\WhatsAppSessionPerUser::setSavesession($UserLogin->company_id,$data['sessionName']))
        {
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_LISTS';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_LISTS';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 