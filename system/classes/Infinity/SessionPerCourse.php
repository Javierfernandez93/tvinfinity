<?php

namespace Infinity;

use HCStudio\Orm;
use HCStudio\Util;

class SessionPerCourse extends Orm {
	protected $tblName = 'session_per_course';
	public static $TEXT = 1;
	public static $VIDEO = 2;
	public static $AUDIO = 3;
	public function __construct() {
		parent::__construct();
	}

    public function getList(int $course_id = null) 
    {
        if(isset($course_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.catalog_multimedia_id,
                        {$this->tblName}.order,
                        {$this->tblName}.course,
                        {$this->tblName}.has_previsualization,
                        {$this->tblName}.create_date,
                        {$this->tblName}.aviable,
                        catalog_multimedia.multimedia,
                        {$this->tblName}.title
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        catalog_multimedia
                    ON 
                        catalog_multimedia.catalog_multimedia_id = {$this->tblName}.catalog_multimedia_id
                    WHERE 
                        {$this->tblName}.status = '1'
                    AND
                        {$this->tblName}.course_id = '{$course_id}'
                    ORDER BY 
                        {$this->tblName}.order 
                    ASC
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
	}
    
    public function getCourseId($session_per_course_id = null) 
    {
        if(isset($session_per_course_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.course_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.status = '1'
                    AND
                        {$this->tblName}.session_per_course_id = '{$session_per_course_id}'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
	}
}