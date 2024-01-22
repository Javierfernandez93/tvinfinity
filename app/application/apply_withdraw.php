<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new MoneyTv\UserSupport;

if($UserSupport->_loaded === true)
{
    if($data['withdraw_per_user_id'])
    {
        $WithdrawPerUser = new MoneyTv\WithdrawPerUser;
        
        if($WithdrawPerUser->loadWhere('withdraw_per_user_id = ?',$data['withdraw_per_user_id']))
        {
            $data['status'] = MoneyTv\WithdrawPerUser::DEPOSITED;
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