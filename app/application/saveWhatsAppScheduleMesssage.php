<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['name'])
    {
        if($data['description'])
        {
            if($whatsapp_message_schedule_id = Infinity\WhatsAppMessageSchedule::saveSchedule($data))
            {
                $data['whatsapp_message_schedule_id'] = $whatsapp_message_schedule_id;
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_SCHEDULES';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_DESCRIPTION';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_NAME';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 