<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$ServicePerClient = new MoneyTv\ServicePerClient;

// if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->_loaded === true)
if(true)
{
    if($services = $ServicePerClient->getAllServices(MoneyTv\ServicePerClient::IN_USE))
    {
        array_map(function($service) use($ServicePerClient) {
            $leftDays = $ServicePerClient->calculateLeftDays($service['active_date'],$service['day']);

            echo "el servicio {$service['service_per_client_id']} tiene {$leftDays} días restantes ";

            if(!$ServicePerClient->isActive($service['active_date'],$service['day']))
            {
                echo " - Expiró ";
                
                if(MoneyTv\ServicePerClient::expireService($service['service_per_client_id']))
                {
                    echo " Correctamente ";
              
                    if($service['autorenew'])
                    {
                        if(MoneyTv\ServicePerClient::requestRenovation($service['client_id'],$service['user_login_id']))
                        {
                            echo " RENOVADO ";
                        }   
                    }
                }
            } else {
                echo " - Esta activo ";
            }
            
            echo "<br>";
        },$services);

        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 