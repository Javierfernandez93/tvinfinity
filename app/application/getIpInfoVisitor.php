<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

if(true)
{	
    if($country = HCStudio\Util::getCountryIP())
    {
        $Country = new World\Country;
        
        if($visitorInfo = $Country->getCountryInfo($country))
        {
            $data['visitorInfo'] = array_merge(['ip'=>$data['ip']??0],$visitorInfo);
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_INFO';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_INFO';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 