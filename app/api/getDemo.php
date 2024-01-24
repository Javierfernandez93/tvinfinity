<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Infinity\UserLogin;

if($UserLogin->logged === true)
{
	if(isset($data['client_id']))
	{
		if($demo = (new Infinity\DemoPerClient)->findRow("client_id = ?",$data['client_id']))
		{
			$data["demo"] = $demo;
			$data["s"] = 1;
			$data["r"] = "DATA_OK";
		} else {
			$data["s"] = 0;
			$data["r"] = "NOT_DEMO";
		}
	} else {
		$data["s"] = 0;
		$data["r"] = "NOT_CLIENT_ID";
	}
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_LOGGED";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 