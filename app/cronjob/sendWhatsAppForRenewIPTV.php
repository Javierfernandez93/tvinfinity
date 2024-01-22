<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

$UserSupport = new MoneyTv\UserSupport;

// $data['PHP_AUTH_USER'] = $data['PHP_AUTH_USER'] ?? false;
// $data['PHP_AUTH_PW'] = $data['PHP_AUTH_PW'] ?? false;

// if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->_loaded === true)
if(true)
{
    if($users = MoneyTv\ServicePerClient::getServicesForSoonExpiration())
    {
        foreach($users as $user)
        {
            if($user['clients'] ?? false)
            {
                $amount = getAmountOfClients($user['clients']);

                if($amount > 0)
                {
                    $grettings = ['😎 Hola','👏🏻 Que tal','🥹 Estimado'];
                    
                    $gretting = $grettings[rand(0,sizeof($grettings)-1)];

                    $message = "*¡{$gretting} ".ucwords(trim($user['name']))."*!, los siguientes *(".$amount.") cliente(s)* que expiran pronto sus servicios:\n\n";
    
                    foreach($user['clients'] as $client)
                    {
                        if($client['aviable_to_alert'])
                        {
                            $message .= "👉 *".trim($client['name'])."* termina en *{$client['left_days']} dia(s)*\n";
                        }
                    }
                    
                    $message .= "\nSi necesitas renovarlos, contáctanos cuánto antes 🕣";

                    MoneyTv\Client::sendWhatsAppForExpiration([
                        'whatsApp' => $user['whatsApp'],
                        'name' => $user['name'],
                        'message' => $message
                    ]);

                    sleep(1);
                }
            }
        }
    }
} else {
    $data['s'] = 0;
    $data['r'] = "INVALID_CREDENTIALS";
}

function getAmountOfClients(array $clients = null) : int {
    return sizeof(array_filter($clients,function($client) {
        return $client['aviable_to_alert'];
    }));
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 