<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{	
    if($images = (new Infinity\Image)->getAll())
    {
        $data['images'] = array_map(function($image) {
            $image['src'] = str_replace("../..",HCStudio\Connection::getMainPath(),$image['src']);
            $image['tag'] = json_decode($image['tag'],true);
            
            return $image;
        },$images);

        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_IMAGES';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 