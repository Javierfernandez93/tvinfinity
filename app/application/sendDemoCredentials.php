<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new MoneyTv\UserSupport;

if($UserSupport->_loaded === true)
{	
    if($data['client_id'])
    {
        if($response = sendWhatsApp($data))
        {
            $data['wa_api'] = $response;
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_SEND_WHATSAPP';
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

function sendWhatsApp(array $data = null) 
{
    return MoneyTv\ApiWhatsApp::sendWhatsAppMessage([
        'message' => MoneyTv\ApiWhatsAppMessages::getIptvSetUpDemoMessage(),
        'image' => null,
        'contact' => [
            "phone" => $data['whatsapp'],
            "name" => $data['name'],
            "user_name" => $data['user_name'],
            "client_password" => $data['client_password']
        ]
    ]);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 