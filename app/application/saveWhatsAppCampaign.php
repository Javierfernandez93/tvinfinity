<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true)
{
    if($data['name'])
    {
        if($data['description'])
        {
            $WhatsAppCampaign = new MoneyTv\WhatsAppCampaign;
            
            if($data['whatsapp_campaign_id'])
            {
                $WhatsAppCampaign->loadWhere("whatsapp_campaign_id = ?",$data['whatsapp_campaign_id']);
            }
            
            $WhatsAppCampaign->user_login_id =  $UserLogin->company_id;
            $WhatsAppCampaign->name = $data['name'];
            $WhatsAppCampaign->description = $data['description'];
            $WhatsAppCampaign->create_date = time();

            if($WhatsAppCampaign->save())
            {
                if($data['lists'])
                {
                    $lists = array_filter($data['lists'],function($list){
                        return $list['selected'];
                    });
                }

                if($lists)
                {
                    $WhatsAppCampaign->whatsapp_list_per_user_ids = json_encode(array_column($lists,'whatsapp_list_per_user_id'));
                    $WhatsAppCampaign->save();
                }

                $data['r'] = 'SAVE_OK';
                $data['s'] = 1;
            }  else {
                $data['r'] = 'NOT_SAVE';
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