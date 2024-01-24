<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

if(true)
{	
    $data = array_merge($data,Infinity\Client::requestRandomData());
    $data['whatsapp'] = $data['phoneCode'].$data['whatsapp'];
    $data['name'] = 'usuario';

    if(Infinity\Client::add(array_merge($data,['user_login_id'=> 1])))
    {
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_SAVE';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 