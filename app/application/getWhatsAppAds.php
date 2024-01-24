<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{	
    $PrintPerBanner = new Infinity\PrintPerBanner;
        
    $data['ads'][] = $PrintPerBanner->getNextBanner($UserLogin->company_id,Infinity\CatalogBanner::WHATSAPP_LEFT);
    $data['ads'][] = $PrintPerBanner->getNextBanner($UserLogin->company_id,Infinity\CatalogBanner::WHATSAPP_RIGHT);

    $data['r'] = 'DATA_OK';
    $data['s'] = 1;
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 