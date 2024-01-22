<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new MoneyTv\UserSupport;

if($UserSupport->_loaded === true)
{
    if(MoneyTv\LicencePerUser::makeLicences($data['company_id'],$data['amount']))
    {
        $data['s'] = 1;
        $data['r'] = 'DATA_OK';
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_ADDED';
    }
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 