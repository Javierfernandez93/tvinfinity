<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

$UserSupport = new Infinity\UserSupport;

$data['PHP_AUTH_USER'] = $data['PHP_AUTH_USER'] ?? null;
$data['PHP_AUTH_PW'] = $data['PHP_AUTH_PW'] ?? null;

if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->_loaded === true)
{
    $BuyPerUser = new Infinity\BuyPerUser;
    
    $activations = $BuyPerUser->getPackageBuysByDate(1); // activations
    $suscriptions = $BuyPerUser->getPackageBuysByDate(5); // monthly subscriptions

    $buys = [...$activations, ...$suscriptions];

    if($buys)
    {
        $UserReferral = new Infinity\UserReferral;
        $ServicePerClient = new Infinity\ServicePerClient;
        
        foreach($buys as $buy)
        {
            echo "ID {$buy['user_login_id']} ";

            if($referrals = $UserReferral->getReferralsIds($buy['user_login_id']))
            {
                echo " con Red <br>";

                foreach($referrals as $user_login_id)
                {
                    echo " - invitado {$user_login_id} ";

                    if($services = $ServicePerClient->getAllServicesSoldForComissions($user_login_id))
                    {
                        echo " con servicios";

                        foreach($services as $service)
                        {
                            $referralName = (new Infinity\UserData)->getName($user_login_id);

                            sendPush($buy['user_login_id'],"Hemos dispersado $0.5 USD por la activación de servicio de tu referido {$referralName}",Infinity\CatalogNotification::GAINS);

                            Infinity\CommissionPerUser::addCreditCommission([
                                'user_login_id' => $buy['user_login_id'],
                                'buy_per_user_id' => $buy['buy_per_user_id'],
                                'catalog_commission_type_id' => Infinity\CatalogCommissionType::NETWORK_TYPE_ID,
                                'service_per_client_id' => $service['service_per_client_id'],
                                'user_login_id_from' => $user_login_id,
                                'amount' => 0.5,
                                'catalog_currency_id' => Infinity\CatalogCurrency::USD,
                                'package_id' => 0,
                            ]);
                        }
                    } else {
                        echo " sin servicios";
                    }
                    echo " <br>";
                }

            } else {
                echo " Sin Red";
            }
            
            echo " <br>";
        }
    }
} else {
    $data['s'] = 0;
    $data['r'] = "INVALID_CREDENTIALS";
}

function sendPush(string $user_login_id = null,string $message = null,int $catalog_notification_id = null) : bool
{
    return Infinity\NotificationPerUser::push($user_login_id,$message,$catalog_notification_id,"");
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 