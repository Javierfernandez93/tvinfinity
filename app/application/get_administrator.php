<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new Infinity\UserSupport;

if($UserSupport->_loaded === true)
{
    if($data['user_support_id'])
    {
        $data["administrator"] = $UserSupport->getUserSupport($data['user_support_id']);
        $data["administrator"]['permissions'] = getPermissions($data['user_support_id']);
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data['r'] = "DATA_ERROR";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function getPermissions(int $user_support_id = null)
{
    $catalog_permissions = (new Infinity\CatalogPermission)->getAll();
    
    if($permissions = (new Infinity\PermissionPerUserSupport)->getPermissions($user_support_id))
    {
        foreach ($catalog_permissions as $key => $catalog_permission) 
        {
            if(in_array($catalog_permission['permission'],$permissions))
            {
                $catalog_permissions[$key]['checked'] = true;
            }
        }
    }

    return $catalog_permissions;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 