<?php

namespace Infinity;

use HCStudio\Orm;

class CommissionPendingFromEwallet extends Orm {
	protected $tblName = 'commission_pending_from_ewallet';
	public $PENDING = 1;
	public $PAYED = 2;
	
	public function __construct() {
		parent::__construct();
	}

	public static function addWithdraw(array $data = null)
	{
		$CommissionPendingFromEwallet = new self;
		$CommissionPendingFromEwallet->user_login_id = $data['user_login_id'];
		$CommissionPendingFromEwallet->catalog_currency_id = $data['catalog_currency_id'];
		$CommissionPendingFromEwallet->wallet_per_user_id = $data['wallet_per_user_id'];
		$CommissionPendingFromEwallet->transaction_per_user_id = $data['transaction_per_wallet_id'];
		$CommissionPendingFromEwallet->amount = $data['ammount'];
		$CommissionPendingFromEwallet->create_date = time();
		
		return $CommissionPendingFromEwallet->save();
	}


	public function getAllDeposited($wallet_per_user_id = null)
	{
		if(isset($wallet_per_user_id))
		{
			$sql = "SELECT 
						SUM({$this->tblName}.amount) as amount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.status = '{$this->PAYED}'
					AND
						{$this->tblName}.wallet_per_user_id = '{$wallet_per_user_id}'
						";

			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getAll($status = null)
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.transaction_per_user_id,
					{$this->tblName}.wallet_per_user_id,
					{$this->tblName}.catalog_currency_id,
					{$this->tblName}.payment_date,
					{$this->tblName}.payment_id,
					{$this->tblName}.retention,
					{$this->tblName}.create_date,
					SUM({$this->tblName}.amount) as amount
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '{$status}'
				GROUP BY 
					{$this->tblName}.catalog_currency_id,
					{$this->tblName}.payment_id,
					{$this->tblName}.wallet_per_user_id
					";

		return $this->connection()->rows($sql);
	}

	public function getAllPayed($from = null,$to = null)
	{
		if (isset($from,$to)) 
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.transaction_per_user_id,
						{$this->tblName}.wallet_per_user_id,
						{$this->tblName}.payment_id,
						{$this->tblName}.payment_date,
						{$this->tblName}.create_date,
						{$this->tblName}.retention,
						{$this->tblName}.amount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.status = '2'
					AND
						{$this->tblName}.payment_date 
					BETWEEN 
						'{$from}'
					AND 
						'{$to}'
						";

			return $this->connection()->rows($sql);
		}
		
		return false;
	}
}