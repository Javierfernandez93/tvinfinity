<?php

namespace Infinity;

use HCStudio\Util;

use JFStudio\Curl;

use Infinity\ApiCredential;

class ApiWhatsApp {
    const END_POINT = 'https://www.zuppi.io/app/services/';
    // const END_POINT = 'http://localhost:8888/mizuum/app/services/';

	public function __construct() {
	}

	public static function getSendWhatsAppUrl()
    {
        return self::END_POINT."sendWhatsApp.php";
    }

	public static function sanitizeContact(array $data = null)
    {
        if($data['phone'])
        {
            $data['phone'] = Util::getNumbers($data['phone']);
        }

        return $data;
    }

	public static function sendWhatsAppMessage(array $data = null)
	{
        if(isset($data) === true)
        {
            $data['contact'] = self::sanitizeContact($data['contact']);

            if(Util::isValidPhone($data['contact']['phone']))
            {
                $Curl = new Curl;            
                
                $Curl->get(self::getSendWhatsAppUrl(), array_merge([
                    'id' => $data['id'] ?? 1,
                    'message' => $data['message'],
                    'image' => $data['image'],
                    'contact' => self::sanitizeContact($data['contact'])
                ],(new ApiCredential)->getApiCredentials(91)));
                
                return $Curl->getResponse(true);
            }

            return false;
        }
        
        return false;
	}
}
