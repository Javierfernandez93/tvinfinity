<?php

namespace MoneyTv;

use HCStudio\Orm;

class CatalogReward extends Orm {
    protected $tblName  = 'catalog_reward';
    public function __construct() {
        parent::__construct();
    }

    public function getAll() 
    {
        $sql = "SELECT
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.title,
                    {$this->tblName}.image,
                    {$this->tblName}.css,
                    {$this->tblName}.description,
                    {$this->tblName}.goal,
                    {$this->tblName}.create_date
                FROM 
                    {$this->tblName}
                WHERE 
                    {$this->tblName}.status = '1'
                ";

        return $this->connection()->rows($sql);
    }
}