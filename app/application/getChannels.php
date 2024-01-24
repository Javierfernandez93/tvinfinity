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
    $data['group'] = $data['group'] ?? null;

    if($data['list_per_user_id'])
    {
        $ListPerUser = new Infinity\ListPerUser;
        
        if($ListPerUser->loadWhere("list_per_user_id = ?",[$data['list_per_user_id']]))
        {
            if($url = Infinity\ListPerUser::concatListUrl($ListPerUser->url))
            {
                if($channels = loadDataFromURL($url,$data['group']))
                {
                    // $Movie = new Infinity\Movie;

                    // array_map(function($channel) use($Movie) {
                    //     if(!$Movie->exist($channel['title']))
                    //     {
                    //         $Movie = new Infinity\Movie;
      
                    //         $Movie->title = $channel['title'];
                    //         $Movie->link = $channel['url'];
                    //         $Movie->image = $channel['image'] ?? 'https://Infinity.site/src/files/img/movie-bg.png';
                    //         $Movie->status = 1;
                    //         $Movie->player = 1;
                    //         $Movie->create_date = time();
                            
                    //         if($Movie->save())
                    //         {
                    //             echo "No existe {$channel['title']} creada \n";
                    //         }
                    //     } else {
                    //         echo "Existe {$channel['title']}\n";
                    //     }
                    // },$channels);

                    $data['channels'] = $channels;
                    $data['r'] = 'DATA_OK';
                    $data['s'] = 1;
                } else {
                    $data['r'] = 'NOT_CHANNELS';
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

function loadDataFromURL(string $url = null,string $group = null) : array
{
    $channels = [];

    $M3uParser = new M3uParser;
    $M3uParser->addDefaultTags();
    
    if($data = $M3uParser->parseFile($url))
    {
        foreach ($data as $key => $entry) {        
            foreach ($entry->getExtTags() as $extTag) {
                switch ($extTag) {
                    case $extTag instanceof \M3uParser\Tag\ExtInf: // If EXTINF tag
                        if($group)
                        {
                            if($extTag->getAttribute('group-title') == $group)
                            {
                                $channels[$key]['title'] = $extTag->getTitle() ?? null;
                                $channels[$key]['image'] = $extTag->getAttribute('tvg-logo') ?? null;
                                $channels[$key]['url'] = $entry->getPath();
                            }
                        } else {
                            $channels[$key]['title'] = $extTag->getTitle() ?? null;
                            $channels[$key]['image'] = $extTag->getAttribute('tvg-logo') ?? null;
                            $channels[$key]['url'] = $entry->getPath();
                        }
                        
                        break;
        
                    case $extTag instanceof \M3uParser\Tag\ExtTv: // If EXTTV tag
                        $channels[$key]['tv_id'] = $extTag->getXmlTvId() ?? null;
                        $channels[$key]['icon'] = $extTag->getIconUrl() ?? null;
                        $channels[$key]['language'] = $extTag->getLanguage() ?? null;
                        break;
                    case $extTag instanceof \M3uParser\Tag\ExtLogo: // If EXTLOGO tag
                        $channels[$key]['logo'] = $extTag->getLogo() ?? null;
                        break;
                }
            }
        }

    }
    
    return array_values($channels);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 