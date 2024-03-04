<?php

namespace Infinity;

use HCStudio\Orm;

class CatalogCurrency extends Orm {
	protected $tblName = 'catalog_currency';
	public static $USD = 1;
	public static $MXN = 2;
	public static $COP = 3;
	public static $BTC = 4;
	public static $PYT = 5;
	public function __construct() {
		parent::__construct();
	}

	public function getCatalogCurrencyName($currency = null) {
		if($currency == self::$USD) {
			return "Dólar";
		} else if($currency == self::$MXN) {
			return "Pesos mexicanos";
		} else if($currency == self::$COP) {
			return "Pesos colombianos";
		} else if($currency == self::$USD) {
			return "Bitcoins";
		} else if($currency == self::$PYT) {
			return "PayToken";
		}
	}

	public function getCatalogCurrencySingleName($currency = null) {
		if($currency == self::$USD) {
			return "USD";
		} else if($currency == self::$MXN) {
			return "MXN";
		} else if($currency == self::$USD) {
			return "COP";
		} else if($currency == self::$USD) {
			return "BTC";
		}
	}
	public function getCurrencyByCountryId($country_id = null) {
		if(isset($country_id) === true)
		{
			$sql = "SELECT 	
						{$this->tblName}.currency
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.country_id = '{$country_id}'
					";

			return $this->connection()->field($sql);
		}
		
		return false;
	}

	public function getCatalogCurrencyByCountryId($country_id = null) 
	{
		$country_id = $country_id == false ? 159 : $country_id; 
		if(isset($country_id) === true)
		{
			$sql = "SELECT 	
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.country_id = '{$country_id}'
					";

			return $this->connection()->field($sql);
		}
		
		return false;
	}


	public function getFullCurrency(int $catalog_currency_id = null)
	{
		$sql = "SELECT 
					{$this->tblName}.description,
					{$this->tblName}.image,
					{$this->tblName}.currency
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.catalog_currency_id = '{$catalog_currency_id}'
				AND  
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->row($sql);
	}
}