<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{	
    if(isset($data['external_client_id']) && !empty($data['external_client_id']))
    {
        if(Infinity\Client::existUser($data['external_client_id'] ?? 0,$UserLogin->company_id))
        {
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_EXIST';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_EXTERNAL_CLIENT_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 