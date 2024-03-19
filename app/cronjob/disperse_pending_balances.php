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

                Infinity\CommissionPerUser::setCommissionAsDispersed($commission['commission_per_user_id'],$transaction_per_wallet_id);

                sendPush($commission['user_login_id'],"Hemos dispersado $ ".number_format($commission['amount'],2)." USD a tu ewallet.",Infinity\CatalogNotification::GAINS);

                doWithdrawal([
                    'user_login_id' => $commission['user_login_id'],
                    'amount' => $commission['amount']
                ]);
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

function doWithdrawal(array $data = null)
{
    /* withdrawal */
    $ReceiverWallet = BlockChain\Wallet::getWallet(BlockChain\Wallet::MAIN_EWALLET);

    if(!$ReceiverWallet)
    {
        return false;
    }
    
    $Wallet = BlockChain\Wallet::getWallet($data['user_login_id']);

    if(!$Wallet)
    {
        return false;
    }

    $message = '';
    
    $transaction_per_wallet_id = $Wallet->createTransaction($ReceiverWallet->public_key,$data['amount'],BlockChain\Transaction::prepareData(['@optMessage'=>$message]),true,BlockChain\Transaction::WITHDRAW_FEE);

    if(!$transaction_per_wallet_id)
    {
        return false;
    }
    
    if(!Infinity\CommissionPendingFromEwallet::addWithdraw([
        'user_login_id' => $data['user_login_id'],
        'wallet_per_user_id' => $ReceiverWallet->getId(),
        'ammount' => $data['amount'],
        'catalog_currency_id' => Infinity\CatalogCurrency::MXN,
        'transaction_per_wallet_id' => $transaction_per_wallet_id
    ]))
    {
        return false;
    }

    return true;
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