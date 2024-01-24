<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$ServicePerClient = new Infinity\ServicePerClient;

// if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->_loaded === true)
if(true)
{
    if($response = Infinity\ApiInfinity::getLastMovies())
    {
        if($response['s'] == 1)
        {
            if(Infinity\Movie::addMoviesTopTen($response['movies']))
            {
                $data["s"] = 1;
                $data["r"] = "SAVE_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_SAVED";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "INVALID_REPONSE";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_REPONSE";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 