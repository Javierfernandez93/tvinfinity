<?php

namespace Infinity;

use HCStudio\Orm;
use JFStudio\Constants;

class Product extends Orm {
	protected $tblName = 'product';

	const EWALLET_SKU = 'PFE';
	const LICENCE_SKU = 'LMN1';
	const CREDIT_SKU = 'CR1';

	public function __construct() {
		parent::__construct();
	}

	public static function hasLicenceSku(string $sku = null) : bool
	{
		return $sku == self::LICENCE_SKU;
	}

	public static function hasCreditSku(string $sku = null) : bool
	{
		return $sku == self::CREDIT_SKU;
	}

	public static function unformatProducts(array $product_ids = null)
	{
		foreach ($product_ids as $product)
		{
			$products[] = array_merge(
				$product,
				(new Product)->getProduct($product['product_id'])
			);
		}

		return $products;
	}

	public function countProducts($in = null,$filter = "AND product.visible = '1'")
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id
				FROM 
					{$this->tblName}
				LEFT JOIN
					catalog_brand
				ON 
					catalog_brand.catalog_brand_id = {$this->tblName}.catalog_brand_id
				LEFT JOIN
					catalog_product
				ON 
					catalog_product.catalog_product_id = {$this->tblName}.catalog_product_id
				WHERE 
					{$this->tblName}.status = '1'
					{$filter}
				AND 
					catalog_product.catalog_product_id IN ({$in})
				GROUP BY
					{$this->tblName}.{$this->tblName}_id
				";
		
		return $this->connection()->column($sql);
	}

	public function getProductsIn($in = null,$in_catalog_products = null,$filter = "AND product.visible = '1'")
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.sku,
					{$this->tblName}.title,
					{$this->tblName}.description,
					{$this->tblName}.keywords,
					{$this->tblName}.create_date,
					{$this->tblName}.update_date,
					{$this->tblName}.visible,
					{$this->tblName}.status,
					catalog_product.catalog_product_id,
					catalog_product.catalog_product,
					catalog_brand.brand
				FROM 
					{$this->tblName}
				LEFT JOIN
					catalog_brand
				ON 
					catalog_brand.catalog_brand_id = {$this->tblName}.catalog_brand_id
				LEFT JOIN
					catalog_product
				ON 
					catalog_product.catalog_product_id = {$this->tblName}.catalog_product_id
				WHERE 
					{$this->tblName}.status = '1'
				AND 
					{$this->tblName}.product_id IN({$in})
					{$filter}
				AND 
					catalog_product.catalog_product_id IN ({$in_catalog_products})
				";
				
		return $this->connection()->rows($sql);
	}

	public function getAllProducts($filter = "AND product.visible = '1'")
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.title,
					{$this->tblName}.promo_price,
					{$this->tblName}.price
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'";
		
		return $this->connection()->rows($sql);
	}

	public function existSku($sku = null)
	{
		if (isset($sku) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.sku
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.sku = '{$sku}'
					AND
						{$this->tblName}.status = '1'
						";
			
			return $this->connection()->field($sql) ? true : false;
		}
		
		return false;
	}
	
	public function getProductIdBySku(string $sku = null)
	{
		if (isset($sku) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.sku = '{$sku}'
					AND
						{$this->tblName}.status = '1'
						";
			
			return $this->connection()->field($sql);
		}
		
		return false;
	}

	public function existCode($code = null)
	{
		if (isset($code) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.code
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.code = '{$code}'
					AND
						{$this->tblName}.status = '1'
						";
			
			return $this->connection()->field($sql) ? true : false;
		}
		
		return false;
	}

	public function getAll($store_per_user_id = null)
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.sku,
					{$this->tblName}.title,
					{$this->tblName}.price,
					{$this->tblName}.promo_price,
					{$this->tblName}.description,
					{$this->tblName}.keywords,
					{$this->tblName}.create_date,
					{$this->tblName}.update_date,
					{$this->tblName}.visible,
					{$this->tblName}.status,
					catalog_product.catalog_product,
					catalog_brand.brand
				FROM 
					{$this->tblName}
				LEFT JOIN
					catalog_brand
				ON 
					catalog_brand.catalog_brand_id = {$this->tblName}.catalog_brand_id
				LEFT JOIN
					catalog_product
				ON 
					catalog_product.catalog_product_id = {$this->tblName}.catalog_product_id
				WHERE 
					{$this->tblName}.status = '1'
				AND 
					{$this->tblName}.store_per_user_id = '{$store_per_user_id}'
				";
		
		return $this->connection()->rows($sql);
	}
	
	public function getProduct(int $product_id = null)
	{
		if(isset($product_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.title,
						{$this->tblName}.sku,
						{$this->tblName}.day,
						{$this->tblName}.amount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					AND 
						{$this->tblName}.product_id = '{$product_id}'
					";
			
			return $this->connection()->row($sql);
		}

		return false;
	}
	
	public function getProductBySku(string $sku = null)
	{
		if(isset($sku) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.title,
						{$this->tblName}.sku,
						{$this->tblName}.amount
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					AND 
						{$this->tblName}.sku = '{$sku}'
					";
			
			return $this->connection()->row($sql);
		}

		return false;
	}

	public function getCount()
	{
		$sql = "SELECT 
					COUNT({$this->tblName}.{$this->tblName}_id) as c
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->field($sql);
	}
	
	public function getIdByCode(string $code = null)
	{
		if (!isset($code)) {
			return false;
		}

		return $this->connection()->field("SELECT 
				{$this->tblName}.{$this->tblName}_id
			FROM 
				{$this->tblName}
			WHERE 
				{$this->tblName}.code = '{$code}'
			AND 
				{$this->tblName}.status != '" . Constants::DELETE . "'
		");
	}
}