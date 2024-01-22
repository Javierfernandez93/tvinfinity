<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true) 
{	
    $data['stats'] = [
        'campaign' => (new MoneyTv\WhatsAppCampaign)->getCount($UserLogin->company_id),
        'message' => (new MoneyTv\WhatsAppCampaign)->getAllListCount($UserLogin->company_id),
        'list' => (new MoneyTv\WhatsAppCampaign)->getAllMessageCount($UserLogin->company_id),
        'contact' => (new MoneyTv\WhatsAppCampaign)->getAllContactsCount($UserLogin->company_id)
    ];
    $data['r'] = 'DATA_OK';
    $data['s'] = 1;
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 