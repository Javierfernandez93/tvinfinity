<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new MoneyTv\UserSupport;

if($UserSupport->_loaded === true)
{
    if($campaigns = (new MoneyTv\CampaignEmail)->getAll())
    {
        $data['campaigns'] = $campaigns;
        $data['s'] = 1;
        $data['r'] = 'DATA_OK';
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_CAMPAIGNS';
    }
} else {
    $data['s'] = 0;
    $data['r'] = 'NOT_FIELD_SESSION_DATA';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 