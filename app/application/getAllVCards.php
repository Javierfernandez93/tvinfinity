<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{	
    $data['aviableToAddVCard'] = (new Infinity\VcardAmountPerUser)->isAbleToAddVcard($UserLogin->company_id);
    $data['aviableToAddVCard'] = true;
    
    if($vcards = (new Infinity\VCardPerUser)->getAll($UserLogin->company_id))
    {
        $data['vcards'] = format($vcards);
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_VCARDS';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function format(array $vcards = null) : array
{
    $VisitPerVCard = new Infinity\VisitPerVCard;

    return array_map(function($vcard) use($VisitPerVCard){
        $vcard['view'] = $VisitPerVCard->getCount($vcard['vcard_per_user_id']);

        return $vcard;
    },$vcards);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 