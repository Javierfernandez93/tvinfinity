<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['catalog_reward_id'])
    {
        if(MoneyTv\RewardPerUser::claim($UserLogin->company_id,$data['catalog_reward_id']))
        {
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_CLAIM';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_CATALOG_REWARD_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 