<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    if($data['commission_per_user_id'])
    {
        $CommissionPerUser = new Infinity\CommissionPerUser;
        
        if($CommissionPerUser->loadWhere('commission_per_user_id = ?',$data['commission_per_user_id']))
        {
            $data['status'] = 2;
            $CommissionPerUser->payment_date = time();
            $CommissionPerUser->status = $data['status'];
        
            if($CommissionPerUser->save())
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