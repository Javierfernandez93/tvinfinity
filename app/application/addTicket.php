<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{
    $data['user_login_id'] = $UserLogin->company_id;

    if($unique_id = Infinity\TicketPerUser::saveTicket($data))
    {
        sendWhatsApp($unique_id);

        $data['s'] = 1;
        $data['r'] = 'DATA_OK';
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_TICKETS';
    }		
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function sendWhatsApp(string $unique_id = null) 
{
    return Infinity\ApiWhatsApp::sendWhatsAppMessage([
        'message' => Infinity\ApiWhatsAppMessages::getTicketCreatedMessage(),
        'image' => null,
        'contact' => [
            "phone" => '+5213317361196',
            "name" => 'Javier',
            "extra" => $unique_id
        ]
    ]);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 