<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    $CommissionPendingFromEwallet = new Infinity\CommissionPendingFromEwallet;
    
    $filter = " WHERE withdraw_per_user.status = '1'";
    
    if($transactions = $CommissionPendingFromEwallet->getAll($filter))
    {
        $data["transactions"] = $transactions;
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_WITHDRAWS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 