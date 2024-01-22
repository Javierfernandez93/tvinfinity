<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['whatsapp_campaign_id'])
    {
        if($campaign = (new MoneyTv\WhatsAppCampaign)->get($data['whatsapp_campaign_id']))
        {
            $data['campaign'] = format($campaign,$UserLogin->company_id);
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_CAMPAIGNS';
            $data['s'] = 1;
        }
    } else {
        $data['r'] = 'NOT_WHATSAPP_CAMPAIGN_ID';
        $data['s'] = 1;
    }   
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function format(array $campaign = null,int $user_login_id = null) : array
{
    $ContactPerWhatsAppList = new MoneyTv\ContactPerWhatsAppList;
    $WhatsAppListPerUser = new MoneyTv\WhatsAppListPerUser;

    if($lists = $WhatsAppListPerUser->getAll($user_login_id))
    {
        $campaign['lists'] = $lists;
        
        $whatsapp_list_per_user_ids = $campaign['whatsapp_list_per_user_ids'];

        $campaign['lists'] = array_map(function($list) use($whatsapp_list_per_user_ids,$ContactPerWhatsAppList) {
            $list['selected'] = $whatsapp_list_per_user_ids ? in_array($list['whatsapp_list_per_user_id'],$whatsapp_list_per_user_ids) : false;
            $list['contacts'] = $ContactPerWhatsAppList->getAll($list['whatsapp_list_per_user_id']);
            
            return $list;
            
        },$campaign['lists']);
    }

    return $campaign;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 