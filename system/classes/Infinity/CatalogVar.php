<?php

namespace Infinity;

use HCStudio\Orm;

class CatalogVar extends Orm {
    protected $tblName  = 'catalog_var';
    public function __construct() {
        parent::__construct();
    }

    public function getAll() 
    {
        $sql = "SELECT
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.name
                FROM 
                    {$this->tblName}
                WHERE 
                    {$this->tblName}.status = '1'
                ";

        return $this->connection()->rows($sql);
    }
    public static function transcode($type = null) 
    {
        if (isset($type) === true) 
        {
            $tpye = strtolower($type);

            if($type === "int")
            {
                return "Entero";
            } else if($type === "text") {
                return "Texto";
            }
        }
    }
}