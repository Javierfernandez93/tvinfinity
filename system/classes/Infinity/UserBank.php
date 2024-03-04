<?php

namespace Infinity;

use HCStudio\Orm;

class UserBank extends Orm {
	protected $tblName = 'user_bank';

	public function __construct() {
		parent::__construct();
	}

	public static function editData(array $data = null) : bool
	{
		$UserData = new self;
		
		$UserData->loadWhere("user_login_id = ?",$data['user_login_id']);
		$UserData->bank = $data['bank'];
		$UserData->clabe = $data['clabe'];
		$UserData->user_login_id = $data['user_login_id'];
		$UserData->account = $data['account'];
		
		return $UserData->save();
	}

	public function getLocalBitcoinAccount($user_login_id = false)
	{
		if (isset($user_login_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.localbitcoin
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					";

			return $this->connection()->field($sql);
		}

		return false;
	}

	public function hasData($user_login_id = false)
	{
		if (isset($user_login_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.user_login_id 
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						(
							{$this->tblName}.airtm != ''
						OR 
							{$this->tblName}.paypal != ''
						OR 
						 	(
									{$this->tblName}.bank != ''
								AND 
									{$this->tblName}.account != ''
								AND 
									{$this->tblName}.clabe != ''
						 	)
						)
					";
					
			return $this->connection()->field($sql) ? true : false;
		}

		return false;
	}

	public function getAirtmAccount($user_login_id = false)
	{
		if (isset($user_login_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.airtm 
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					";
					
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getPayPalAccount($user_login_id = false)
	{
		if (isset($user_login_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.paypal 
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					";
					
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getBankAccount($user_login_id = false)
	{
		if (isset($user_login_id) === true) 
		{
			$sql = "SELECT 
						CONCAT_WS(' ',{$this->tblName}.bank,{$this->tblName}.account,{$this->tblName}.clabe) as bank 
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					";
					
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getBankData($user_login_id = false)
	{
		if (isset($user_login_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.bank,
						{$this->tblName}.account,
						{$this->tblName}.clabe
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					";
					
			return $this->connection()->row($sql);
		}

		return false;
	}

	public function getBitcoinAccount($user_login_id = false)
	{
		if (isset($user_login_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.localbitcoin
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					";
					
			return $this->connection()->field($sql);
		}

		return false;
	}

	
	public function getAll($filter = false){
		$db = $this->connection();
		$filter = ($filter) ? $filter : " ORDER BY user_account.user_login_id ASC";
		$sql = "SELECT * FROM user_bank {$filter}";
		return $db->rows($sql);
	}
}