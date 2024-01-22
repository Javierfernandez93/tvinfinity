<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true)
{	
	if($intents = (new MoneyTv\IntentChat)->getAllGroup($UserLogin->company_id))
	{
		$data['intents'] = format($intents);
		$data['r'] = 'DATA_OK';
		$data['s'] = 1;
	} else {
		$data['r'] = 'NOT_INTENTS';
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

function format(array $intents = null) : array
{
	$IntentChat = new MoneyTv\IntentChat;
	$ReplyPerCatalogTagIntentChat = new MoneyTv\ReplyPerCatalogTagIntentChat;

	return array_map(function($intent) use($IntentChat,$ReplyPerCatalogTagIntentChat) {
		$intent['words'] = $IntentChat->getAllWords($intent['catalog_tag_intent_chat_id']);
		$intent['replys'] = $ReplyPerCatalogTagIntentChat->getReply($intent['catalog_tag_intent_chat_id']);

		return $intent;
	},$intents);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 