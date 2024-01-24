<?php

namespace Infinity;

use JFStudio\Curl;

class ApiInfinity {
    const END_POINT = 'http://44.193.240.148:3000/';
    // const END_POINT = 'http://localhost:3000/';

	public function __construct() {
	}

	public static function getUsersUrl()
    {
        return self::END_POINT."user/get";
    }

	public static function getDemoUrl()
    {
        return self::END_POINT."user/demo";
    }
	
    public static function getServiceUrl()
    {
        return self::END_POINT."user/service";
    }

    public static function getLastMoviesUrl()
    {
        return self::END_POINT."movies/last";
    }

    public static function getRenovationUrl()
    {
        return self::END_POINT."user/renovation";
    }

    public static function getExistUserUrl()
    {
        return self::END_POINT."user/exist";
    }
    
    public static function getFullUrl()
    {
        return self::END_POINT."user/full";
    }

	public static function getUser(string $id = null)
	{
        if(isset($id) === true)
        {
            $Curl = new Curl;      

            $Curl->get(self::getUsersUrl(), [
                'id' => $id,
            ]);

            $response = $Curl->getResponse(true);
            
            return $response['s'] == 1 ? true : $response;
        }
        
        return false;
	}
	
    public static function requestFull(string $id = null)
	{
        if(isset($id) === true)
        {
            $Curl = new Curl;      

            $Curl->get(self::getFullUrl(), [
                'id' => $id,
            ]);

            $response = $Curl->getResponse(true);
            
            return $response;
        }
        
        return false;
	}
	
    public static function getLastMovies()
	{
        $Curl = new Curl;      

        $Curl->get(self::getLastMoviesUrl(), []);

        $response = $Curl->getResponse(true);
        
        return $response;
	}
    
    public static function existUser(string $user_id = null)
	{
        $Curl = new Curl;      

        $Curl->get(self::getExistUserUrl(), [
            'id' => $user_id,
        ]);

        $response = $Curl->getResponse(true);
        
        return $response;
	}
	
    public static function getRenovation(string $id = null,int $package_id = null)
	{
        if(isset($id) === true)
        {
            $Curl = new Curl;      

            $Curl->get(self::getRenovationUrl(), [
                'id' => $id,
                'package_id' => $package_id
            ]);

            $response = $Curl->getResponse(true);
            
            return $response;
        }
        
        return false;
	}
	
    public static function generateDemo(string $username = null,string $password = null)
	{
        if(isset($username,$password) === true)
        {
            $Curl = new Curl;      

            $Curl->get(self::getDemoUrl(), [
                'username' => $username,
                'password' => $password,
            ]);

            $response = $Curl->getResponse(true);
            
            return $response['s'] == 1 ? $response : false;
        }
        
        return false;
	}

    public static function generateService(string $username = null,string $password = null,int $package_id = null)
	{
        if(isset($username) === true)
        {
            $Curl = new Curl;      

            $Curl->get(self::getServiceUrl(), [
                'username' => $username,
                'password' => $password,
                'package_id' => $package_id
            ]);

            $response = $Curl->getResponse(true);
            
            return $response['s'] == 1 ? $response : false;
        }
        
        return false;
	}
}
