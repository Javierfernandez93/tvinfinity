<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new MoneyTv\UserSupport;

if($UserSupport->_loaded === true)
{
    if($data['ticket_per_user_id'])
	{
        $TicketPerUser = new MoneyTv\TicketPerUser;

        if($TicketPerUser->loadWhere('ticket_per_user_id = ?',$data['ticket_per_user_id']))
        {
            if($data['status'] == MoneyTv\TicketPerUser::SUPPORTING)
            {
                $TicketPerUser->user_support_id = $UserSupport->getId();
            } else if($data['status'] == MoneyTv\TicketPerUser::FINISHED) {
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
    return MoneyTv\ApiWhatsApp::sendWhatsAppMessage([
        'message' => MoneyTv\ApiWhatsAppMessages::getTicketDoneMessage(),
        'image' => null,
        'contact' => [
            "phone" => (new MoneyTv\UserContact)->getWhatsApp($user_login_id),
            "name" => (new MoneyTv\UserData)->getName($user_login_id),
            "extra" => $unique_id
        ]
    ]);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 