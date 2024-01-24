<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 
require_once TO_ROOT . 'vendor/autoload.php';

use M3uParser\M3uParser;

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

// if($UserLogin->logged === true)
if(true)
{	
    $data['list_per_user_id'] = $data['list_per_user_id'] ?? false;

    if($data['list_per_user_id'])
    {
        $ListPerUser = new Infinity\ListPerUser;
        
        if($ListPerUser->loadWhere("list_per_user_id = ?",[$data['list_per_user_id']]))
        {
            if($url = Infinity\ListPerUser::concatListUrl($ListPerUser->url))
            {
                if($groups = loadDataFromURL($url,$ListPerUser->has_group))
                {
                    $data['groups'] = $groups;
                    $data['r'] = 'DATA_OK';
                    $data['s'] = 1;
                } else {
                    $data['r'] = 'NOT_groups';
                    $data['s'] = 0;
                }    
            } else {
                $data['r'] = 'NOT_URL';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_LIST_PER_USER';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_LIST_PER_USER_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function loadDataFromURL(string $url = null,bool $has_group = false) : array
{
    $groups = [];

    $m3uParser = new M3uParser;
    $m3uParser->addDefaultTags();

    if($data = $m3uParser->parseFile($url))
    {
        foreach ($data as $entry) {
            foreach ($entry->getExtTags() as $extTag) {
                switch ($extTag) {
                    case $extTag instanceof \M3uParser\Tag\ExtInf: // If EXTINF tag
                        $group = $extTag->getAttribute('group-title');

                        if(!in_array($group,$groups))
                        {
                            $groups[] = $group;
                        }
                    break;
                }
            }
        }

    }
    
    return $groups;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 