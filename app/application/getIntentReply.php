<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php";
require_once TO_ROOT . 'system/mlm/vendor/autoload.php';

use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\CrossValidation\StratifiedRandomSplit;

$data = HCStudio\Util::getHeadersForWebService();

if($data['sessionName'])
{
	if($data['message'])
	{	
		$data['user_login_id'] = (new MoneyTv\WhatsAppSessionPerUser)->getUserBySessionName($data['sessionName']);

		$IntentChat = new MoneyTv\IntentChat;

		if($temp_intents = $IntentChat->getAllLike($data['user_login_id'],$data['message']))
		{
			$intents = removeKey($temp_intents);
		} else {
			$intents = $IntentChat->getAll($data['chat_per_sheet_id']);
		}
		
		if($intents)
		{
			if($tag = predict($intents,$data['message']))
			{
				$data = array_merge($data,(new MoneyTv\WhatsAppSessionPerUser)->getVars($data['user_login_id']));
				$reply = MoneyTv\Parser::doParser(getReply($tag['tag']),$data);
				
				if(saveMessage($data['user_login_id'],$message,MoneyTv\ChatSender::$ROBOT))
				{
					$data['s'] = 1;
					$data['reply'] = $reply;
				} else {
					$data['s'] = 0;
					$data['r'] = 'NOT_SAVE_MESSAGE';
				}
			} else {
				$data['s'] = 0;
				$data['r'] = 'NOT_TAG';
			}
		} else {
			$data['s'] = 0;
			$data['r'] = 'NOT_INTENTS';
		}
	} else {
		$data['s'] = 0;
		$data['r'] = "NOT_MESSAGE";
	}
} else {
	$data['s'] = 0;
	$data['r'] = "NOT_SESSIONNAME";
}

function saveMessage($chat_per_sheet_id = null,$message = null,$send_from = null)
{
	return true;

	if(isset($chat_per_sheet_id,$message,$send_from) === true)
	{
		$ConversationPerChat = new MoneyTv\ConversationPerChat;
		$ConversationPerChat->chat_per_sheet_id = $chat_per_sheet_id;
		$ConversationPerChat->create_date = time();
		$ConversationPerChat->message = $message;
		$ConversationPerChat->send_from = $send_from;

		if($ConversationPerChat->save()) {
			return $ConversationPerChat->getId();
		}
	}

	return false;
}

function isSingleData(array $data = null) 
{
	return sizeOf(array_unique(array_column($data,"tag"))) === 1;
} 

function analitycs(array $data = null,string $room_id = null)
{
	if (isset($data,$room_id) === true) 
	{
		if($data['tag'] == "support")
		{
			$Room = new MoneyTv\Room;
			if($Room->loadWhere("room_id = ?",$room_id))
			{
				$Room->need_attend = "1";
				$Room->save();
			}
		}
	}
}

function predict(array $data = null, string $sentence = null) 
{
	if(isSingleData($data) === true)
	{
		return [
			"tag" => $data[0]['tag'],
			"probability" => 100
		];
	} else {
		$data = array_values($data);

		$classifier = new SVC(
		    Kernel::LINEAR, // $kernel
		    1.0,            // $cost
		    3,              // $degree
		    null,           // $gamma
		    0.0,            // $coef0
		    0.001,          // $tolerance
		    100,            // $cacheSize
		    true,           // $shrinking
		    true            // $probabilityEstimates, set to true
		);

		$ML = new JFStudio\ML;

		$samples = $ML->getTargetsByBD($data);
		$targets = $ML->getSamplesByBD($data);

		$vectorizer = new TokenCountVectorizer(new WordTokenizer);
		$tfIdfTransformer = new TfIdfTransformer;

		$vectorizer->fit($samples);
		$vectorizer->transform($samples);

		$tfIdfTransformer->fit($samples);
		$tfIdfTransformer->transform($samples);

		$dataset = new ArrayDataset($samples, $targets);
		$randomSplit = new StratifiedRandomSplit($dataset, 0.1);

		$ML->setValues($ML->convertValues($dataset->getSamples()));
		$ML->setVocabulary($vectorizer->getVocabulary());

		$ts = $randomSplit->getTrainSamples();
		$tl = $randomSplit->getTrainLabels();

		$classifier->train($samples, $tl);

		$samples = $ML->getSamples($sentence);

		if($ML->hasSamples($samples) === true)
		{
			if($tag = $classifier->predict($samples))
			{
				return [
					"tag" => $tag,
					"probability" => $classifier->predictProbability($ML->getSamples($sentence))
				];
			}
		}
	}

	return false;
}

function getReply(string $tag = null) : string
{
	$CatalogTagIntentChat = new MoneyTv\CatalogTagIntentChat;

	if($CatalogTagIntentChat->loadWhere("tag = ?",$tag))
	{
		return (new MoneyTv\ReplyPerCatalogTagIntentChat)->getReplyRandom($CatalogTagIntentChat->getId());
	}

	return MoneyTv\ReplyPerCatalogTagIntentChat::getDefaultReply();
}

function removeKey(array $data = null) : array
{
	foreach ($data as $key => $value) {
		unset($data[$key]['rel1']);
	}

	return $data;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 