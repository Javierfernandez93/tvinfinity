<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

sendWhatsApp(1);

function sendWhatsApp(int $user_login_id = null) 
{
    return MoneyTv\ApiWhatsApp::sendWhatsAppMessage([
        'message' => MoneyTv\ApiWhatsAppMessages::getWelcomeMessage(),
        'image' => null,
        'contact' => [
            "phone" => (new MoneyTv\UserContact)->getWhatsApp($user_login_id),
            "name" => (new MoneyTv\UserData)->getName($user_login_id)
        ]
    ]);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 