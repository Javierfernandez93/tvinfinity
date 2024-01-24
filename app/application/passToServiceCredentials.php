<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{	
    if($data['client_id'])
    {
        if($response = Infinity\ServicePerClient::sendDemoToServiceCredentials($data))
        {   
            $data['wa_api'] = $response;
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_ADD_SERVICE';
            $data['s'] = 1;
        }
    } else {
        $data['r'] = 'NOT_CLIENT_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 