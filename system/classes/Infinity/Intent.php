<?php

namespace Infinity;

use HCStudio\Orm;

class Intent extends Orm {
    protected $tblName  = 'intent';
    public function __construct() {
        parent::__construct();
    }
    public function getAll($filter = "") 
    {
    	$sql = "SELECT 
    				{$this->tblName}.{$this->tblName}_id,
    				{$this->tblName}.words,
    				catalog_tag_intent.tag
    			FROM 
    				{$this->tblName}
    			LEFT JOIN 
    				catalog_tag_intent
    			ON
    				catalog_tag_intent.catalog_tag_intent_id = {$this->tblName}.catalog_tag_intent_id
                    {$filter}
    			ORDER BY 
    				catalog_tag_intent.catalog_tag_intent_id
    			ASC
    			";

    	return $this->connection()->rows($sql);
    }

    public function getAllLike($words = null) 
    {
        if(isset($words) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.words,
                        catalog_tag_intent.tag,
                        MATCH(`words`) AGAINST ('{$words}' IN BOOLEAN MODE) as rel1
                    FROM 
                        {$this->tblName}  
                    LEFT JOIN 
                        catalog_tag_intent
                    ON
                        catalog_tag_intent.catalog_tag_intent_id = {$this->tblName}.catalog_tag_intent_id
                    WHERE 
                    MATCH 
                        (words) 
                    AGAINST 
                        ('{$words}' IN BOOLEAN MODE)
                    ORDER BY 
                        rel1
                    DESC
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }
}