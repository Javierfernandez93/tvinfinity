<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data['utm'])
{
    if($data['landing_per_user_id'] = (new MoneyTv\LandingPerUser)->getLandingIdByRoute($data['utm'],$data['catalog_landing_id']))
    {
        if(MoneyTv\VisitPerLanding::addVisit($data['landing_per_user_id'],HCStudio\Util::getIP(),$data['catalog_landing_id']))
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_DATA";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_LANDING_PER_USER_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 