<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data["email"])
{
    $Token = new HCStudio\Token;

    $token = explode("[",$data['token']);
    $token = [
        'key' => substr($token[1],0,strlen($token[1])-1),
        'token' => $token[0],
    ];
    
    if($Token->checkToken($token))
    {    
        $UserLogin = new MoneyTv\UserLogin;

        if($UserLogin->isUniqueMail($data['email']) === false)
        {
            $UserLogin = new MoneyTv\UserLogin;

            if($UserLogin->isUniqueMail($Token->params['email']) === false)
            {
                if($UserLogin->cargarDonde("email = ?",$Token->params['email']))
                {
                    $UserLogin->password = sha1($data['password']);
                    
                    if($UserLogin->save())
                    {
                        $data["s"] = 1;
                        $data["r"] = "SAVE_OK";
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_SENT";
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_TOKEN";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_FOUND_MAIL";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_FOUND_MAIL";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "INVALID_TOKEN";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 