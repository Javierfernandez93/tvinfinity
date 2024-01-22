<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true)
{	
    if($licence = (new MoneyTv\LicencePerUser)->getLicenceActive($UserLogin->company_id))
    {
        if($reamingTime = HCStudio\Util::unixDiff(time(),strtotime("+30 days",$licence['active_date'])))
        {
            $data['reamingTime'] = $reamingTime;
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_PACKAGE_ID';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_LICENCE';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 