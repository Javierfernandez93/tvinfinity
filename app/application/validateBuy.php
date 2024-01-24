<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if(($data['user'] == HCStudio\Util::USERNAME && $data['password'] == HCStudio\Util::PASSWORD) || $UserSupport->_loaded === true)
{
    if($data['invoice_id'])
	{
        $BuyPerUser = new Infinity\BuyPerUser;
        
        if($BuyPerUser->isInvoicePending($data['invoice_id']))
        {
            if($BuyPerUser->loadWhere('invoice_id = ?',$data['invoice_id']))
            {	
                if(Infinity\BuyPerUser::processPayment($BuyPerUser->getId()))
                {
                    $BuyPerUser->catalog_validation_method_id = $data['catalog_validation_method_id'];
                    $BuyPerUser->ipn_data = isset($data['ipn_data']) ? $data['ipn_data'] : '';
                    $BuyPerUser->approved_date = time();
                    $BuyPerUser->user_support_id = $data['user_support_id'] ? $data['user_support_id'] : $BuyPerUser->user_support_id;
                    $BuyPerUser->status = Infinity\BuyPerUser::VALIDATED;

                    if($BuyPerUser->save())
                    {   
                        // if(sendEmail((new Infinity\UserLogin)->getEmail($BuyPerUser->user_login_id),$BuyPerUser->invoice_id))
                        // {
                        //     $data['mail_sent'] = true;
                        // }

                        $data['status'] = $BuyPerUser->status;
                        $data['s'] = 1;
                        $data['r'] = 'SAVE_OK';
                    } else {
                        $data['s'] = 0;
                        $data['r'] = 'NOT_UPDATE';
                    }
                } else {
                    $data['s'] = 0;
                    $data['r'] = 'NOT_PROCESSED';
                }
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_SAVE';
            } 		
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_PEDNING';
        } 		
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_ITEMS';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function sendEmail(string $email = null,string $invoice_id = null) : bool
{
    if(isset($email,$invoice_id) === true)
    {
        require_once TO_ROOT . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $Layout = JFStudio\Layout::getInstance();
            $Layout->init("",'buy',"mail-new",TO_ROOT.'/apps/applications/',TO_ROOT.'/');

            $Layout->setScriptPath(TO_ROOT . '/apps/admin/src/');
    		$Layout->setScript(['']);

            $CatalogMailController = Infinity\CatalogMailController::init(1);

            $Layout->setVar([
                "invoice_id" => $invoice_id,
                "email" => $email
            ]);

            $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF; // PHPMailer\PHPMailer\SMTP::DEBUG_SERVER
            $mail->isSMTP(); 
            // $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Host = $CatalogMailController->host;
            $mail->SMTPAuth = true; 
            $mail->Username = $CatalogMailController->mail;
            $mail->Password =  $CatalogMailController->password;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = $CatalogMailController->port; 

            //Recipients
            $mail->setFrom($CatalogMailController->mail, $CatalogMailController->sender);
            $mail->addAddress($email, $names);     

            //Content
            $mail->isHTML(true);                                  
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Pago aceptado';
            $mail->Body = $Layout->getHtml();
            $mail->AltBody = strip_tags($Layout->getHtml());

            return $mail->send();
        } catch (Exception $e) {
            
        }
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 