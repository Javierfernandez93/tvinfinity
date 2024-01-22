<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

$UserSupport = new MoneyTv\UserSupport;

$data['PHP_AUTH_USER'] = $data['PHP_AUTH_USER'] ?? false;
$data['PHP_AUTH_PW'] = $data['PHP_AUTH_PW'] ?? false;

// if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->_loaded === true)
if(true)
{
    if($data['services'] = (new MoneyTv\ServicePerClient)->getAllForAutorenovation())
    {
        $data['services'] = array_map(function($service){
            if(MoneyTv\ServicePerClient::requestRenovation($service['client_id'],$service['user_login_id']))
            {
                $service['renovated'] = true;
            }

            return $service;
        },$data['services']);

        $data['s'] = 1;
        $data['r'] = "DATA_OK";
    } else {
        $data['s'] = 0;
        $data['r'] = "NOT_SERVICES";
    }
} else {
    $data['s'] = 0;
    $data['r'] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 