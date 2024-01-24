<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true) 
{	
    $data['stats'] = [
        'campaign' => (new Infinity\WhatsAppCampaign)->getCount($UserLogin->company_id),
        'message' => (new Infinity\WhatsAppCampaign)->getAllListCount($UserLogin->company_id),
        'list' => (new Infinity\WhatsAppCampaign)->getAllMessageCount($UserLogin->company_id),
        'contact' => (new Infinity\WhatsAppCampaign)->getAllContactsCount($UserLogin->company_id)
    ];
    $data['r'] = 'DATA_OK';
    $data['s'] = 1;
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 