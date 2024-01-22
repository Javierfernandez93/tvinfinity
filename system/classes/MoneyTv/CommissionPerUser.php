<?php

namespace MoneyTv;

use HCStudio\Orm;
use HCStudio\Util;

use MoneyTv\UserReferral;
use MoneyTv\CatalogCurrency;
use MoneyTv\CatalogCommissionType;

use JFStudio\Constants;

class CommissionPerUser extends Orm {
	protected $tblName = 'commission_per_user';
    
	//** status */
	const PENDING_FOR_DISPERSION = 1;
	const COMPLETED = 2;
	const REMANENT_ID = 1;
	public function __construct() {
		parent::__construct();
	}

	public static function addCreditCommission(array $data = null) : bool
	{
		if((new CommissionPerUser)->existCreditCommission($data['service_per_client_id']) == false)
		{
			return self::add($data);
		}

		return false;
	}

	public static function add(array $data = null) : bool
	{
		$CommissionPerUser = new CommissionPerUser;
		$CommissionPerUser->user_login_id = $data['user_login_id'];
		$CommissionPerUser->user_login_id_from = $data['user_login_id_from'];
		$CommissionPerUser->buy_per_user_id = $data['buy_per_user_id'];
		$CommissionPerUser->service_per_client_id = $data['service_per_client_id'];
		$CommissionPerUser->catalog_commission_type_id = $data['catalog_commission_type_id'];
		$CommissionPerUser->package_id = $data['package_id'];
		$CommissionPerUser->amount = $data['amount'];
		
		$CommissionPerUser->catalog_currency_id = CatalogCurrency::USD;
		$CommissionPerUser->create_date = time();
		$CommissionPerUser->status = self::PENDING_FOR_DISPERSION;
		
		return $CommissionPerUser->save();
	}

	public static function saveCommissionsByCatalogCommission(array $catalog_commission,array $item = null,int $user_login_id_from = null,int $buy_per_user_id = null) : bool
	{
		if($catalog_commission['commission_type'] == CatalogCommissionType::NETWORK)
		{
			$network = (new UserReferral)->getSponsorByReverseLevel(2, $user_login_id_from);
			
			$amount = $catalog_commission['is_percentaje'] ? Util::getPercentaje($item['amount'], $catalog_commission['amount']) : $catalog_commission['amount'];

			$user_login_id = isset($network[$catalog_commission['level'] - 1]) ? $network[$catalog_commission['level'] - 1] : self::REMANENT_ID;
			$user_login_id = $user_login_id == 0 ? self::REMANENT_ID : $user_login_id;
		
			$CommissionPerUser = new CommissionPerUser;

			if($CommissionPerUser->existCommission($user_login_id,$user_login_id_from,$item['package_id'],$buy_per_user_id,$catalog_commission['catalog_commission_type_id']) == false)
			{
				$CommissionPerUser->user_login_id = $user_login_id;
				$CommissionPerUser->buy_per_user_id = $buy_per_user_id;
				$CommissionPerUser->catalog_commission_type_id = $catalog_commission['catalog_commission_type_id'];
				$CommissionPerUser->user_login_id_from = $user_login_id_from;
				$CommissionPerUser->amount = $amount;
				$CommissionPerUser->catalog_currency_id = CatalogCurrency::USD;
				$CommissionPerUser->package_id = $item['package_id'];
				$CommissionPerUser->status = self::PENDING_FOR_DISPERSION;
				$CommissionPerUser->create_date = time();
				
				return $CommissionPerUser->save();
			}
		}

		return false;
	}

	public static function saveCommissionsByCatalogCommissions(array $catalog_commissions,array $item = null,int $user_login_id_from = null,int $buy_per_user_id = null) : bool
	{
		$saved = 0;

		foreach ($catalog_commissions as $catalog_commission)
		{
			if(self::saveCommissionsByCatalogCommission($catalog_commission,$item,$user_login_id_from,$buy_per_user_id))
			{
				$saved++;
			}
		}

		return sizeof($catalog_commissions) == $saved;
	}

	public static function saveCommissionsByItems(array $items,int $user_login_id_from = null,int $buy_per_user_id = null)
	{
		if(isset($items,$user_login_id_from) === true)
		{
			foreach($items as $item)
			{
				self::saveCommissionsByCatalogCommissions($item['catalog_commission'],$item,$user_login_id_from,$buy_per_user_id);
			}
		}
	}
	
	public function existCommission(int $user_login_id,int $user_login_id_from = null,int $package_id = null,int $buy_per_user_id = null,int $catalog_commission_type_id = null) : bool
	{
		if(isset($user_login_id,$user_login_id_from,$package_id,$buy_per_user_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id_from = '{$user_login_id_from}'
					AND 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.package_id = '{$package_id}'
					AND 
						{$this->tblName}.buy_per_user_id = '{$buy_per_user_id}'
					AND 
						{$this->tblName}.catalog_commission_type_id = '{$catalog_commission_type_id}'
					AND 
						{$this->tblName}.status != '".Constants::DELETE."'
					";

			return $this->connection()->field($sql) ? true : false;
		}

		return false;
	}
	
	public function existCreditCommission(int $service_per_client_id = null) : bool
	{
		if(isset($service_per_client_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.service_per_client_id = '{$service_per_client_id}'
					AND 
						{$this->tblName}.status != '".Constants::DELETE."'
					";

			return $this->connection()->field($sql) ? true : false;
		}

		return false;
	}

	public static function setCommissionAsDispersed(int $commission_per_user_id,int $transaction_per_wallet_id = null)
	{
		if(isset($commission_per_user_id,$transaction_per_wallet_id) === true)
		{
			$CommissionPerUser = new CommissionPerUser;
			
			if($CommissionPerUser->loadWhere('commission_per_user_id = ?',$commission_per_user_id))
			{
				$CommissionPerUser->deposit_date = time();
				$CommissionPerUser->status = self::COMPLETED;
				$CommissionPerUser->transaction_per_wallet_id = $transaction_per_wallet_id;
				
				return $CommissionPerUser->save();
			}
		}

		return false;
	}

	public function getPendingCommissions() 
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.user_login_id,
					{$this->tblName}.user_login_id_from,
					{$this->tblName}.amount
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '".self::PENDING_FOR_DISPERSION."'
				";

		return $this->connection()->rows($sql);
	}
	
	public function getAll(int $user_login_id = null)  
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.user_login_id,
						{$this->tblName}.buy_per_user_id,
						{$this->tblName}.package_id,
						{$this->tblName}.catalog_currency_id,
						{$this->tblName}.deposit_date,
						{$this->tblName}.transaction_per_wallet_id,
						{$this->tblName}.catalog_commission_type_id,
						{$this->tblName}.user_login_id_from,
						{$this->tblName}.create_date,
						catalog_currency.currency,
						catalog_commission_type.commission_type,
						user_data.names,
						{$this->tblName}.status,
						{$this->tblName}.amount
					FROM 
						{$this->tblName}
					LEFT JOIN
						catalog_currency 
					ON 
						catalog_currency.catalog_currency_id = {$this->tblName}.catalog_currency_id 
					LEFT JOIN
						catalog_commission_type 
					ON 
						catalog_commission_type.catalog_commission_type_id = {$this->tblName}.catalog_commission_type_id 
					LEFT JOIN
						user_data 
					ON 
						user_data.user_login_id = {$this->tblName}.user_login_id_from 
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND
						{$this->tblName}.status IN (".self::PENDING_FOR_DISPERSION.",".self::COMPLETED.")
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}

	public function getSum(int $user_login_id = null,string $filter = null)  
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT 
						SUM({$this->tblName}.amount) as amount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND
						{$this->tblName}.status IN (".self::PENDING_FOR_DISPERSION.",".self::COMPLETED.")
						{$filter}
					";

			return $this->connection()->field($sql);
		}

		return false;
	}
}