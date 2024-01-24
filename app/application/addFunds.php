<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{
    if($BuyPerUser = saveBuy($data,$UserLogin))
    {	
        $data['buy_per_user_id'] = $BuyPerUser->getId();

        if($checkout_data = createTransaction($BuyPerUser,$UserLogin))
        {
            $BuyPerUser->checkout_data = json_encode($checkout_data);
            
            if($BuyPerUser->save())
            {
                $data['checkout_data'] = $checkout_data;
                $data['s'] = 1;
                $data['r'] = 'SAVE_OK';
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_UPDATE';
            }
        } else { 
            $data['s'] = 0;
            $data['r'] = 'NOT_CHECKOUT_DATA';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_SAVE';
    } 
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function createTransaction(Infinity\BuyPerUser $BuyPerUser = null,Infinity\UserLogin $UserLogin = null)
{
	if($BuyPerUser->catalog_payment_method_id == Infinity\CatalogPaymentMethod::COINPAYMENTS)
	{
		return createTransactionFromCoinPayments($BuyPerUser,$UserLogin);
	} else if($BuyPerUser->catalog_payment_method_id == Infinity\CatalogPaymentMethod::PAYPAL) {
		return createTransactionPayPal($BuyPerUser,$UserLogin);
	} else if($BuyPerUser->catalog_payment_method_id == Infinity\CatalogPaymentMethod::EWALLET) {
		return createTransactionFromEwallet($BuyPerUser,$UserLogin);
	}
}

function createTransactionPayPal(Infinity\BuyPerUser $BuyPerUser = null,Infinity\UserLogin $UserLogin = null)
{
	require_once TO_ROOT . "/system/vendor/autoload.php";

	$apiContext = new \PayPal\Rest\ApiContext(
	    new \PayPal\Auth\OAuthTokenCredential(
	        JFStudio\PayPal::CLIENT_ID,
	        JFStudio\PayPal::CLIENT_SECRET
	    )
	);

	$apiContext->setConfig(['mode' => JFStudio\PayPal::MODE]);

	$payer = new \PayPal\Api\Payer;
	
    $payer->setPaymentMethod('paypal');

    $total = $BuyPerUser->amount+$BuyPerUser->fee; 
	
    $amount = new \PayPal\Api\Amount;
	$amount->setTotal((string)$total);
	$amount->setCurrency('USD');
	$amount->setDetails($details);

	$transaction = new \PayPal\Api\Transaction;
	$transaction->setAmount($amount);
    $transaction->setInvoiceNumber($BuyPerUser->getId());
    
	$redirectUrls = new \PayPal\Api\RedirectUrls;
	$redirectUrls->setReturnUrl(JFStudio\PayPal::RETURN_URL)
	    ->setCancelUrl(JFStudio\PayPal::CANCEL_URL);

	$payment = new \PayPal\Api\Payment;
	$payment->setIntent('sale')
	    ->setPayer($payer)
	    ->setTransactions(array($transaction))
	    ->setRedirectUrls($redirectUrls);

	try {
	    $payment->create($apiContext);

		return [
			'checkout_url' => $payment->getApprovalLink(),
			'txn_id' => $payment->getId(),
			'fee' => $BuyPerUser->fee,
			'unix_time' => time(),
			'amount' => $BuyPerUser->amount,
			'total' => $total
		];
	} catch (\PayPal\Exception\PayPalConnectionException $ex) {
	    echo $ex->getData();
	}
}

function createTransactionFromEwallet(Infinity\BuyPerUser $BuyPerUser = null,Infinity\UserLogin $UserLogin = null)
{
	return [
		'amount' => $BuyPerUser->amount,
		'txn_id' => $BuyPerUser->invoice_id,
		'unix_time' => time(),
		'checkout_url' => "../../apps/ewallet/process?txn_id={$BuyPerUser->invoice_id}"
	];
}

function createTransactionFromCoinPayments(Infinity\BuyPerUser $BuyPerUser = null,Infinity\UserLogin $UserLogin = null)
{
	try {
		require_once TO_ROOT .'/vendor2/autoload.php';

		$CoinpaymentsAPI = new CoinpaymentsAPI(JFStudio\CoinPayments::PRIVATE_KEY, JFStudio\CoinPayments::PUBLIC_KEY, 'json');

		$req = [
			'amount' => $BuyPerUser->amount,
			'currency1' => 'USD',
			'currency2' => $BuyPerUser->getCurrency(),
			'buyer_name' => $UserLogin->getNames(),
			'buyer_email' => $UserLogin->email,
			'item_name' => "Pago de orden {$BuyPerUser->invoice_id}",
			'custom' => $BuyPerUser->invoice_id,
			'item_number' => $BuyPerUser->invoice_id,
			'address' => '', // leave blank send to follow your settings on the Coin Settings page
			'ipn_url' => 'https://www.Infinity.site/app/cronjob/ipn_coinpayments.php',
		];
						
		$result = $CoinpaymentsAPI->CreateCustomTransaction($req);

		if ($result['error'] == 'ok') {
	
			return $result['result'];
		} else {
			print 'Error: '.$result['error']."\n";
		}
	} catch (Exception $e) {
		echo 'Error: ' . $e->getMessage();
		exit();
	}	
}

function saveBuy($data = null,$UserLogin = null)
{
	$BuyPerUser = new Infinity\BuyPerUser;
	$BuyPerUser->user_login_id = $UserLogin->company_id;
	$BuyPerUser->fee = Infinity\BuyPerUser::getFee($data['catalog_payment_method_id'],$data['amount']);
	$BuyPerUser->item = Jcart\Cart::formatFundsItems($data['amount']);
	$BuyPerUser->checkout_data = json_encode([]);
	$BuyPerUser->ipn_data = json_encode([]);
	$BuyPerUser->invoice_id = Jcart\Cart::getInstanceId();
	$BuyPerUser->shipping = 0;
	$BuyPerUser->catalog_payment_method_id = $data['catalog_payment_method_id'];
	$BuyPerUser->catalog_currency_id = $data['catalog_currency_id'] ? $data['catalog_currency_id'] : Infinity\CatalogCurrency::USD;
	$BuyPerUser->amount = $data['amount'];
	$BuyPerUser->create_date = time();
	
	return $BuyPerUser->save() ? $BuyPerUser : false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 