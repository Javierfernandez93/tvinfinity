<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    if($data['ticket_per_user_id'])
	{
        $TicketPerUser = new Infinity\TicketPerUser;

        if($TicketPerUser->loadWhere('ticket_per_user_id = ?',$data['ticket_per_user_id']))
        {
            if($data['status'] == Infinity\TicketPerUser::SUPPORTING)
            {
                $TicketPerUser->user_support_id = $UserSupport->getId();
            } else if($data['status'] == Infinity\TicketPerUser::FINISHED) {
                sendWhatsApp($TicketPerUser->user_login_id,$TicketPerUser->unique_id);
            }

            $TicketPerUser->status = $data['status'];
            
            if($TicketPerUser->save())
            {
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_SAVED';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_TICKET_PER_USER';
            $data['s'] = 1;
        }
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_TICKET_PER_USER_ID';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function sendWhatsApp(int $user_login_id = null,string $unique_id = null) 
{
    return Infinity\ApiWhatsApp::sendWhatsAppMessage([
        'message' => Infinity\ApiWhatsAppMessages::getTicketDoneMessage(),
        'image' => null,
        'contact' => [
            "phone" => (new Infinity\UserContact)->getWhatsApp($user_login_id),
            "name" => (new Infinity\UserData)->getName($user_login_id),
            "extra" => $unique_id
        ]
    ]);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 