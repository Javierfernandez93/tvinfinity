<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new MoneyTv\UserSupport;

if($UserSupport->_loaded === true)
{
    if($data['notice_id'])
    {
        $Notice = new MoneyTv\Notice;
        
        if($Notice->cargarDonde("notice_id = ?",$data['notice_id']))
        {
            $Notice->status = MoneyTv\Notice::DELETED;
            
            if($Notice->save())
            {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_UPDATE";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_NOTICE";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_NOTICE_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 