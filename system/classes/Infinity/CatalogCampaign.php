<?php

namespace Infinity;

use HCStudio\Orm;

class CatalogCampaign extends Orm
{
	const DEFAULT_CAMPAING = 'signup';
	protected $tblName = 'catalog_campaing';
	public function __construct()
	{
		parent::__construct();
	}

	public function getUtm(int $catalog_campaing_id = null)
	{
		if (isset($catalog_campaing_id) === true) {
			$sql = "SELECT 
						{$this->tblName}.utm
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_campaing_id = '{$catalog_campaing_id}'
					AND 
						{$this->tblName}.status = '1'
					";

			if ($utm = $this->connection()->field($sql)) {
				return $utm;
			}
		}

		return self::DEFAULT_CAMPAING;
	}

	public function getCatalogCampaing(string $utm = null)
	{
		if (isset($utm) === true) {
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.utm = '{$utm}'
					AND 
						{$this->tblName}.status = '1'
					";

			if ($catalog_campaing_id = $this->connection()->field($sql)) {
				return $catalog_campaing_id;
			}
		}

		return 0;
	}
}
