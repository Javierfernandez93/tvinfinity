<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    $Broker = new Infinity\Broker;
    
    if($Broker->cargarDonde('broker_id = ?',$data['broker_id']))
    {
        $Broker->name = $data['name'];
        $Broker->fee = $data['fee'];

        if($Broker->save())
        {
            $data["s"] = 1;
            $data["r"] = "SAVE_OK";    
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_SAVE";    
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_SAVE";    
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 