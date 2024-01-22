<?php

namespace MoneyTv;

use HCStudio\Orm;
use HCStudio\Connection;

class ListPerUser extends Orm {
    protected $tblName  = 'list_per_user';

    const MAIN_URL_PATH  = '/src/files/m3u8/';

    public function __construct() {
        parent::__construct();
    }
    
    public static function concatListUrl(string $url = null) : string {
        return Connection::getMainPath().self::MAIN_URL_PATH.$url;
    }

    public function _getAll(int $user_login_id = null) : array|null
    {
        if($lists = $this->getAll($user_login_id))
        {
            return array_map(function($list){
                $list['url'] = self::concatListUrl($list['url']);

                return $list;
            },$lists);
        }

        return null;
    }

    public function getAll(int $user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.url,
                        {$this->tblName}.title,
                        {$this->tblName}.description,
                        {$this->tblName}.image,
                        {$this->tblName}.has_group,
                        {$this->tblName}.create_date
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->rows($sql);
        }
    }
    
    public static function getListUrl(int $list_per_user_id = null) 
    {
        if(isset($list_per_user_id) === true)
        {
            if($url = (new ListPerUser)->get($list_per_user_id))
            {
                return self::concatListUrl($url);
            }
        }
    }

    public function get(int $list_per_user_id = null) 
    {
        if(isset($list_per_user_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.url
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.list_per_user_id = '{$list_per_user_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql);
        }
    }
}