<?php

namespace Infinity;

use JFStudio\Curl;

class Lattv
{
    const FORCE_PRODUCTION = true;	
	const URL_PRODUCTION = 'http://xyz.lattv.com.co:8080/vTXIdSCb/';	
	const URL_DEVELOPER = 'http://localhost:3000/';	

	const API_KEY = 'DA960C17230276C94346F1F1F01B8214';	

    const URLS = [
        'user_info' => 'action=user_info',
    ];

    public static function getUrlMainPath() : string
    {
        if(self::FORCE_PRODUCTION)
        {
            return self::URL_PRODUCTION;
        }
        
        if($_SERVER['SERVER_NAME'] == 'localhost')
        {
            return self::URL_DEVELOPER;
        }

        return self::URL_PRODUCTION;
    }

    public static function getURL(string $urlName = null) : string
    {
        return self::getUrlMainPath().self::URLS[$urlName];
    }

    public static function call(string $action = null,array $data = [])
	{
		$Curl = new Curl;

        $data = [
            ...$data,
            ...[
                "action" => $action,
                "api_key" => self::API_KEY,
            ]
        ];

        // d(self::getUrlMainPath().http_build_query($data));

        $Curl->get(self::getUrlMainPath()."?".http_build_query($data));

        return $Curl->getResponse(true);
	}
}