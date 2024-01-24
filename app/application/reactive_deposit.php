<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    $CatalogPlan = new Infinity\CatalogPlan;

    if($data['transaction_requirement_per_user_id'])
    {
        $TransactionRequirementPerUser = new Infinity\TransactionRequirementPerUser;
        
        if($TransactionRequirementPerUser->isAviableToReactive($data['transaction_requirement_per_user_id']))
        {
            if($TransactionRequirementPerUser->cargarDonde('transaction_requirement_per_user_id = ?',$data['transaction_requirement_per_user_id']))
            {
                $TransactionRequirementPerUser->status = Infinity\TransactionRequirementPerUser::PENDING;

                if($TransactionRequirementPerUser->save())
                {
                    $data["s"] = 1;
                    $data["r"] = "DATA_OK";
                } else {
                    $data['r'] = "NOT_UPDATE_TRANSACTION_REQUIREMENT_PER_USER";
                    $data['s'] = 0;
                }
            } else {
                $data['r'] = "NOT_TRANSACTION_REQUIREMENT_PER_USER";
                $data['s'] = 0;
            }
        } else {
            $data['r'] = "NOT_AVIABLE_TO_REACTIVE";
            $data['s'] = 0;
        }
    } else {
        $data['r'] = "NOT_TRANSACTION_REQUIREMENT_PER_USER_ID";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 