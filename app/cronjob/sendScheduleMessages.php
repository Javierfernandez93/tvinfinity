<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

$UserSupport = new MoneyTv\UserSupport;

// if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->_loaded === true)
if(true)
{
    $WhatsAppMessageSchedule = new MoneyTv\WhatsAppMessageSchedule;
    
    if($schedules = $WhatsAppMessageSchedule->getAllPending())
    {
        array_map(function($schedule) use($WhatsAppMessageSchedule) {
            try {
                $WhatsAppMessageSchedule->sendMessage(
                    [
                        'id' => $schedule['sessionName'], 
                        'message' => $schedule['message']['content'],
                        'image' => $schedule['message']['image'],
                        'contacts' => $schedule['contacts']
                    ]
                );
    
                MoneyTv\WhatsAppMessageSendPerContact::setMessagesAsSent($schedule['contacts']);
                MoneyTv\WhatsAppMessageSchedule::setScheduleAsSent($schedule['whatsapp_message_schedule_id']);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }

        },$schedules);
    }
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 