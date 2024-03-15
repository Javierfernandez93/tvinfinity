<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    if($data['commission_pending_from_ewallet_id'])
    {
        $CommissionPendingFromEwallet = new Infinity\CommissionPendingFromEwallet;
        
        if($CommissionPendingFromEwallet->loadWhere('commission_pending_from_ewallet_id = ?',$data['commission_pending_from_ewallet_id']))
        {
            $data['status'] = -1;
            $CommissionPendingFromEwallet->status = $data['status'];
        
            if($CommissionPendingFromEwallet->save())
            {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_SAVE";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_WITHDRAW_PER_USER";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_WITHDRAW_PER_USER_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 