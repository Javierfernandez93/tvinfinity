<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    if($data['withdraw_per_user_id'])
    {
        $WithdrawPerUser = new Infinity\WithdrawPerUser;
        
        if($WithdrawPerUser->loadWhere('withdraw_per_user_id = ?',$data['withdraw_per_user_id']))
        {
            $data['status'] = Infinity\WithdrawPerUser::DELETED;
            $WithdrawPerUser->status = $data['status'];
        
            if($WithdrawPerUser->save())
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