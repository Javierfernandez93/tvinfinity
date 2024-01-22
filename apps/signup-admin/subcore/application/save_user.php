<?php define("TO_ROOT","../../../..");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$data = $data["data"];

$UserSupport = new CronosUser\UserSupport;
	
if($data["user_support"]["email"])
{
	$UserSupport->deleteSession();
	
	if($UserSupport->isAbiableToSingUp())
	{
		if($UserSupport->isUniqueMail($data["user_support"]["email"]))
		{ 	
		 	$data["user_support"]["password"] = sha1($data["user_support"]["password"]);

			$UserSupport->cargarArray($data["user_support"]);
			$UserSupport->active = '1';
			$UserSupport->image = "https://steamuserimages-a.akamaihd.net/ugc/865110015591450218/759FB5794FF46D59FC54C511DE09DE0E2C387B21/";

			if($UserSupport->save())
			{
				if(sendMail($UserSupport))
				{
					$data["mail_sent"] = true;
				}
				$data["s"]=1;
				$data["r"]='<t>Tu cuenta fue creada correctamente. Estamos redirigiéndote a tu portal.</t>';
			} else {
				$data["s"]=0;
				$data["r"]='No se pudo guardar tu información personal';				
			}
		} else {
			$data["s"]=0;
	 		$data["r"]='ingrese sus datos de sesión correctamente';
		}		
	} else {
		$data["s"]=0;
		$data["r"]='Correo ya registrado';
	}		
}else{
	$data["s"]=0;
	$data["r"]='Correo Invalido';
}

function sendPush($company_id = null)
{
	if(isset($company_id) === true)
	{
		$UserToken = new CronosUser\UserToken;

		if($UserToken->cargarDonde("user_login_id = ?",$company_id))
		{
			$UserData = new CronosUser\UserData;

			if($names = $UserData->getNames($company_id))
			{
				$Push = JFStudio\Push::getInstance();

				$params = [
					'title' => "Videoconsulta confirmada",
					'body' => "Ya hemos hecho un espacio para atenderte",
					'message' => "Ya hemos hecho un espacio para atenderte",
					'tokens' => $UserToken->token,
					'param' => $company_id,
					'activity_id' => JFStudio\ModelNotificationRoutes::$GROUP,
					'turn_on_light' => false,
					'alarm_sound' => false,
					'ajust_sound' => false,
					'vibration_pattern' => false,
					'icon' => 1,
					'url' => 'https://www.sumateahora.com/apps/backoffice/',
					'click_action' => "https://www.youtube.com/watch?v=WoGWPN3DWHk",
				];

				return $Push->sendAlert($params);
			}
		}
	}

	return false;
}

function sendMail($UserSupport = null)
{	
	if(isset($UserSupport) === true)
	{
		require_once TO_ROOT . '/system/SendGrid/vendor/autoload.php'; // If you're using Composer (recommended)

		$Mailer = new JFStudio\MailerSendGrid(3);
		$SendGrid = new \SendGrid("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");
		$Email = new \SendGrid\Mail\Mail(); 

		$MailLayout = JFStudio\Layout::getInstance();
		$MailLayout->init("Cuenta creada","account_confirmed","mail","",TO_ROOT."/",TO_ROOT."/apps/mail/");

		$MailLayout->setScriptPath(TO_ROOT . '/apps/admin/src/');
		$MailLayout->setScript(['']);

		$MailLayout->setVar([
			"phone" => $UserSupport->phone,
			"names" => $UserSupport->names,
			"email" => $UserSupport->email
		]);

		# compania
	  	$data_mailer["mail_from"] = "descubrecronos@gmail.com";
	  	$data_mailer["name_from"] = "Cronos";

		# quien se registra
	  	$data_mailer["name_to"] = $UserSupport->names;
		$data_mailer["mail_to"] = $UserSupport->email;

	  	$data_mailer["subject"] = "Cronos® - Cuenta médica creada";
		$data_mailer["body"] = html_entity_decode($MailLayout->getViewContent());

		if(!$Mailer->_sendMail($data_mailer,$Email,$SendGrid)) return false;

		unset($SendGrid);
		unset($Email);
		return true;
	}
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 