<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    $CatalogPlan = new Infinity\CatalogPlan;
    
    if($CatalogPlan->isAviableProfit($data['catalog_plan_id'],$data['additional_profit'],$data['sponsor_profit']))
    {
        $UserWallet = new Infinity\UserWallet;
                
        if($UserWallet->getSafeWallet($data['user_login_id']))
        {   
            $data['deposit'] = $data['ammount'] - $data['originalAmmount'];

            if($data['deposit'] > 0)
            {
                if($UserWallet->doTransaction($data['deposit'],Infinity\Transaction::DEPOSIT,null,null,false))
                {
                    $data["transaction_done"] = true;
                }
            }

            $UserPlan = new Infinity\UserPlan;

            if($UserPlan->setPlan($UserWallet->user_login_id,$data['additional_profit'],$data['sponsor_profit']))
            {   
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_UPDATE_PLAN";
            }
        } else {
            $data['r'] = "DATA_ERROR";
            $data['s'] = 0;
        }
    } else {
        $data['MAX_PROFIT'] = Infinity\CatalogPlan::MAX_PROFIT;
        $data['r'] = "PROFIT_EXCEDS_MAX_LIMIT";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 