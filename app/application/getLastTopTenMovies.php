<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new MoneyTv\UserLogin;

if($UserLogin->logged === true)
{	
    if($movies = (new MoneyTv\Movie)->getLastTopTenMovies())
    {
        $data['movies'] = $movies;
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_MOVIES';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 