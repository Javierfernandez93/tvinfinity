<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

$UserSupport = new Infinity\UserSupport;

set_time_limit(0);

$data['PHP_AUTH_USER'] = $data['PHP_AUTH_USER'] ?? false;
$data['PHP_AUTH_PW'] = $data['PHP_AUTH_PW'] ?? false;

if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->logged === true)
{
    $CommissionPerUser = new Infinity\CommissionPerUser;
    $UserLogin = new Infinity\UserLogin(false,false);
    $BuyPerUser = new Infinity\BuyPerUser;
    
    $dispertions = [];
    
    if($commissions = $CommissionPerUser->getPendingCommissions())
    {
        foreach($commissions as $commission)
        {
            $message = 'COMISIÓN';

            if($transaction_per_wallet_id = send($commission['user_login_id'],$commission['amount'],$message))
            {
                $dispertions[] = $commission;

                $CommissionPerUser::setCommissionAsDispersed($commission['commission_per_user_id'],$transaction_per_wallet_id);

                sendPush($commission['user_login_id'],"Hemos dispersado $ ".number_format($commission['amount'],2)." USD a tu ewallet.",Infinity\CatalogNotification::GAINS);
            }
        }
    }

    $data['dispertions'] = $dispertions;
    $data['s'] = 1;
    $data['r'] = "DATA_OK";
} else {
    $data['s'] = 0;
    $data['r'] = "INVALID_CREDENTIALS";
}

function sendPush(string $user_login_id = null,string $message = null,int $catalog_notification_id = null) : bool
{
    return Infinity\NotificationPerUser::push($user_login_id,$message,$catalog_notification_id,"");
}

function send(int $user_login_id = null,float $amountToSend = null,string $message = null)
{
    if($ReceiverWallet = BlockChain\Wallet::getWallet($user_login_id))
    {
        if($amountToSend)
        {
            $Wallet = BlockChain\Wallet::getWallet(BlockChain\Wallet::MAIN_EWALLET);

            if($transaction_per_wallet_id = $Wallet->createTransaction($ReceiverWallet->public_key,$amountToSend,BlockChain\Transaction::prepareData(['@optMessage'=>$message]),true))
            {
                return $transaction_per_wallet_id;
            } 
        } 
    } 
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 