<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    if($data['company_id'])
    {
        $UserLogin = new Infinity\UserLogin;

        if($UserLogin->cargarDonde("company_id = ?",$data['company_id'])) 
        {
            $UserLogin->status = Infinity\UserLogin::DELETED;

            if($UserLogin->save())
            {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data['r'] = "NOT_SAVE";
                $data['s'] = 0;    
            }
        } else {
            $data['r'] = "NOT_LOADED";
            $data['s'] = 0;    
        }
    } else {
        $data['r'] = "DATA_ERROR";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 