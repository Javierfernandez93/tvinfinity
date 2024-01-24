<?php

namespace Infinity;

use HCStudio\Orm;
use JFStudio\Constants;

class Course extends Orm {
	protected $tblName = 'course';

    /* target > 0 package_id */
    const ALL = -1;
    const INACTIVES = 0;
    
	public function __construct() {
		parent::__construct();
	}

	public function getList()
    {
        $sql = "SELECT 
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.title,
                    {$this->tblName}.description,
                    {$this->tblName}.price,
                    {$this->tblName}.create_date,
                    {$this->tblName}.target,
                    {$this->tblName}.image,
                    catalog_course.name,
                    catalog_course_type.catalog_course_type_id,
                    catalog_course_type.type,
                    CONCAT_WS(' ',user_support.names,user_support.last_name,user_support.sur_name) as names
                FROM 
                    {$this->tblName}
                LEFT JOIN 
                    catalog_course
                ON
                    catalog_course.catalog_course_id = {$this->tblName}.catalog_course_id
                LEFT JOIN 
                    catalog_course_type
                ON
                    catalog_course_type.catalog_course_type_id = {$this->tblName}.catalog_course_type_id
                LEFT JOIN 
                    user_support
                ON
                    user_support.user_support_id = {$this->tblName}.user_support_id
                WHERE 
                    {$this->tblName}.status = '".Constants::AVIABLE."'
                ";
        
        return $this->connection()->rows($sql);
	}

    public function get($course_id = null) 
    {
        if(isset($course_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.title,
                        {$this->tblName}.description,
                        {$this->tblName}.price,
                        {$this->tblName}.create_date,
                        {$this->tblName}.image,
                        user_support.image as user_image,
                        catalog_course.name,
                        catalog_course_type.type
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        catalog_course
                    ON
                        catalog_course.catalog_course_id = {$this->tblName}.catalog_course_id
                    LEFT JOIN 
                        catalog_course_type
                    ON
                        catalog_course_type.catalog_course_type_id = {$this->tblName}.catalog_course_type_id
                    LEFT JOIN 
                        user_support
                    ON
                        user_support.user_support_id = {$this->tblName}.user_support_id
                    WHERE 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    AND
                        {$this->tblName}.{$this->tblName}_id = '{$course_id}'
                    ";
            
            return $this->connection()->row($sql);
        }

        return false;
	}
}