<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true)
{
    if($Wallet = BlockChain\Wallet::getWallet($UserLogin->company_id))
    {
        $users = (new MoneyTv\UserReferral)->getReferralCount($UserLogin->company_id);
        $credits = (new MoneyTv\CreditPerUser)->getCreditsAmount($UserLogin->company_id);
        $amount = (new MoneyTv\CommissionPerUser)->getSum($UserLogin->company_id);
        
        $data['balance'] = [
            'amount' => $amount ? $amount : 0,
            'users' => $users ? $users : 0,
            'credits' => $credits ? $credits : 0,
        ];
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_WALLET';
        $data['s'] = 1;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 