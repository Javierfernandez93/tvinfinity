<?php

namespace Infinity;

use HCStudio\Orm;

class ProductPermission extends Orm {
	protected $tblName = 'product_permission';

	const ACTIVE = 1;
	const INACTIVE = 0;
	const DELETED = -1;
	
	public function __construct() {
		parent::__construct();
	}

	public static function add(array $data = null) 
    {
        if(!isset($data))
        {
            return false;
        }

        $ProductPermission = new self;
        
        if($ProductPermission->hasPermission($data))
        {
            return false;
        }

        $ProductPermission->loadArray($data);
        
        return $ProductPermission->save();
    }
    
	public function getProductEndDate(array $data = null)
    {
        if(!isset($data))
        {
            return false;
        }

        return $this->connection()->field("SELECT 
                {$this->tblName}.end_date
            FROM 
                {$this->tblName}
            WHERE 
                {$this->tblName}.user_login_id = '{$data['user_login_id']}'
            AND 
                {$this->tblName}.product_id = '{$data['product_id']}'
            AND
                {$this->tblName}.status = '".self::ACTIVE."'
        ");
    }

	public static function hasPermission(array $data = null)
	{
        if(!isset($data))
        {
            return false;
        }

		$end_date = (new self)->getProductEndDate($data);

		if(!$end_date)
        {
            return false;
        }

        return time() < $end_date;
	}
}