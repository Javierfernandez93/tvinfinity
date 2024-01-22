<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new MoneyTv\UserSupport;

if($UserSupport->_loaded === true)
{
    if($data['title'])
    {
        if($data['description'])
        {
            if($data['video'])
            {
                if(!(new MoneyTv\Landing)->existPath($data['path']))
                {
                    if(MoneyTv\Landing::add($data))
                    {
                        $data["s"] = 1;
                        $data["r"] = "DATA_OK";
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_SAVE";
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "ALREADY_EXIST_ROUTE";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_VIDEO";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_CONTENT";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_TITLE";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 