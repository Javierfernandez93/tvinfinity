<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

if($data['email'])
{
    $UserLogin = new Infinity\UserLogin;

    if($UserLogin->isUniqueMail($data['email']))
    {
        if($user_login_id = $UserLogin->doSignup($data))
        {
            if(sendEmailUser($data['email'],$data['names'],$data['password']))
            {
                $data['email_sent'] = true;
            }

            if(sendPushUser($user_login_id,$data['names']))
            {
                $data['push_sent'] = true;
            }

            if(sendEmailSponsor($data['referral']['user_login_id'],$data['names']))
            {
                $data['email_sponsor_sent'] = true;
            }

            if(sendPushSponsor($data['referral']['user_login_id'],$data['names']))
            {
                $data['push_sponsor_sent'] = true;
            }
            
            if(sendWhatsApp($user_login_id))
            {
                $data['whatsapp_sent'] = true;
            }

            if($UserLogin->login($data['email'],sha1($data['password'])))
            {
                $data['s'] = 1;
                $data['r'] = 'LOGGED_OK';
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_LOGGED';
            }
        } else {
            $data['s'] = 0;
            $data['r'] = 'ERROR_ON_SIGNUP';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'MAIL_ALREADY_EXISTS';
    }
} else {
	$data['s'] = 0;
	$data['r'] = 'NOT_FIELD_SESSION_DATA';
}

function sendWhatsApp(int $user_login_id = null) 
{
    return Infinity\ApiWhatsApp::sendWhatsAppMessage([
        'message' => Infinity\ApiWhatsAppMessages::getWelcomeMessage(),
        'image' => null,
        'contact' => [
            "phone" => (new Infinity\UserContact)->getWhatsApp($user_login_id),
            "name" => (new Infinity\UserData)->getName($user_login_id)
        ]
    ]);
}

function sendPush(string $user_login_id = null,string $message = null,int $catalog_notification_id = null) : bool
{
    return Infinity\NotificationPerUser::push($user_login_id,$message,$catalog_notification_id,"");
}

function sendPushUser(string $user_login_id = null,string $names = null) : bool
{
    return sendPush($user_login_id,"Bienvenido a bordo {$names}, estamos felices de que te hayas registrado en Funnels7",Infinity\CatalogNotification::ACCOUNT);
}

function sendPushSponsor(string $user_login_id = null,string $names = null) : bool
{
    return sendPush($user_login_id,"Felicitaciones, {$names} se unió a tu grupo de referidos",Infinity\CatalogNotification::REFERRAL);
}

function sendEmailSponsor(string $user_login_id = null,string $names = null) : bool
{
    if(isset($user_login_id,$names) === true)
    {
        $UserLogin = new Infinity\UserLogin;

        if($email = $UserLogin->getEmail($user_login_id))
        {
            return sendEmail($email,$names,null,'Nuevo afiliado en Funnels7','partnerWelcome');
        }
    }

    return false;
}

function sendEmailUser(string $email = null,string $names = null,$password = null) : bool
{
    if(isset($email,$names) === true)
    {
        return sendEmail($email,$names,$password,'Bienvenido a bordo','welcome');
    }

    return false;
}

function sendEmail(string $email = null,string $names = null,$password = null,string $subject = null,string $view = null) : bool
{
    if(isset($email,$names) === true)
    {
        require_once TO_ROOT . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $Layout = JFStudio\Layout::getInstance();
            $Layout->init("",$view,"mail-new",TO_ROOT.'/apps/applications/',TO_ROOT.'/');

            $Layout->setScriptPath(TO_ROOT . '/apps/admin/src/');
    		$Layout->setScript(['']);

            $CatalogMailController = Infinity\CatalogMailController::init(1);

            $Layout->setVar([
                "email" => $email,
                "password" => $password,
                "names" => $names
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
            $mail->Subject = $subject;
            $mail->Body = $Layout->getHtml();
            $mail->AltBody = strip_tags($Layout->getHtml());

            return $mail->send();
        } catch (Exception $e) {
            
        }
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 