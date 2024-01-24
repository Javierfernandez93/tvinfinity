<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

// if($UserLogin->logged === true)
if(true)
{	
    // if($lists = (new Infinity\ListPerUser)->_getAll($UserLogin->company_id))
    if($lists = (new Infinity\ListPerUser)->_getAll(65))
    {
        $data['lists'] = $lists;
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_LISTS';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 