<?php

namespace MoneyTv;

use HCStudio\Orm;

use MoneyTv\DemoPerClient;
use MoneyTv\ServicePerClient;
use HCStudio\Token;
use MoneyTv\UserData;
use MoneyTv\ApiWhatsApp;
use MoneyTv\ApiMoneyTv;
use MoneyTv\ApiWhatsAppMessages;

class Client extends Orm {
  protected $tblName  = 'client';
  const RANDOM_USER_DATA_LENGHT = 3;
  const PASSWORD_DEFAULT = 8;
  const TOKEN_LENGHT = 2;
  public function __construct() {
    parent::__construct();
  }
  
  public static function addCredentials(array $data = null) : bool
  {
    $Client = new Client;
    
    if($Client->loadWhere('client_id = ?',$data['client_id']))
    {
        $Client->external_client_id = $data['external_client_id'];
        $Client->user_name = $data['user_name'];
        $Client->client_password = $data['client_password'];
        $Client->active_date = time();

        return $Client->save();
    }

    return false;
  }

  public static function existUser(int $external_client_id = null) 
  {
    $response = ApiMoneyTv::existUser($external_client_id);

    if($response['s'] == 1)
    {
      return $response['found'];
    }

    return false;
  }

  public static function sendDemo(array $data = null) 
  {
    return DemoPerClient::sendDemo($data);
  }
  
  public static function sendService(int $service_per_client_id = null) 
  {
    return ServicePerClient::sendService($service_per_client_id);
  }

  public static function generateUserName(string $name = null) : string
  {
    return isset($name) ? explode(" ",$name)[0].Token::__randomKey(self::TOKEN_LENGHT) : Token::__randomKey(self::TOKEN_LENGHT*2);
  }

  public static function requestRandomData() : array 
  {
    $token = Token::__randomKey(self::RANDOM_USER_DATA_LENGHT); 

    return [
      'name' => $token,
      'email' => self::requestRandomEmail($token),
    ];
  }

  public static function requestRandomEmail(string $token = null) : string
  {
    return $token.'@moneytv.site';
  }

  public static function add(array $data = null) 
  {
    $Client = new self;

    $data['name'] = str_replace(" ","",$data['name']);
    
    // if(!$Client->exist($data['email']))
    if(true)
    {
        // $Client->loadWhere("whatsapp = ?",$data['whatsapp']);

        // removing spaces 
        $Client->name = $data['name'];
        $Client->user_login_id = $data['user_login_id'];
        $Client->whatsapp = $data['whatsapp'];
        $Client->email = isset($data['email']) ? $data['email'] : '';
        $Client->create_date = time();

        if($Client->save())
        {
          if(filter_var($data['demo']['enabled'], FILTER_VALIDATE_BOOLEAN))
          {
            if($demo_per_client_id = DemoPerClient::add(array_merge($data['demo'],[
                'client_id' => $Client->getId(),
                'adult' => isset($data['adult']) ? $data['adult'] : true
              ])))
              {
                // return  self::requestDemoWhatsApp("Quieren una demo para {$data['name']}");
                
                if(self::sendDemo([
                  'client_id' => $Client->getId(),
                  'demo_per_client_id' => $demo_per_client_id
                ]))
                {
                  return $Client->findRow("client_id = ?",$Client->getId()); 
                }
              }
          } else {
            if($service_per_client_id = ServicePerClient::add(array_merge($data['service'],[
              'user_login_id' => $data['user_login_id'],
              'client_id' => $Client->getId(),
              'month' => $data['service']['month'],
              'adult' => filter_var($data['service']['adult'] ?? 1, FILTER_VALIDATE_INT)
            ])))
            {
              // return self::requestServiceWhatsApp("Quieren un servicio para {$data['name']}");

              if(self::sendService($service_per_client_id))
              {
                return $Client->findRow("client_id = ?",$Client->getId()); 
              }
            }
          }
        }
    }

    return false;
  }

  public static function sendWhatsAppDemo(int $user_login_id = null)
  {
      return ApiWhatsApp::sendWhatsAppMessage([
          'message' => ApiWhatsAppMessages::getNewDemoMessage(),
          'image' => null,
          'contact' => [
              "phone" => '573503637342',
              "name" => (new UserData)->getName($user_login_id)
          ]
      ]);
  }
  
  public static function sendWhatsAppRenovation(int $user_login_id = null)
  {
      return ApiWhatsApp::sendWhatsAppMessage([
          'message' => ApiWhatsAppMessages::getRenovationMessage(),
          'image' => null,
          'contact' => [
              "phone" => '5213317361196',
              // "phone" => '573503637342',
              "name" => (new UserData)->getName($user_login_id)
          ]
      ]);
  }
  
  public static function sendWhatsAppService(int $user_login_id = null)
  {
      return ApiWhatsApp::sendWhatsAppMessage([
          'message' => ApiWhatsAppMessages::getNewServiceMessage(),
          'image' => null,
          'contact' => [
              "phone" => '573503637342',
              "name" => (new UserData)->getName($user_login_id)
          ]
      ]);
  }
  public function getNames(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                LOWER(CONCAT_WS(' ',
                  {$this->tblName}.names,
                  {$this->tblName}.last_name,
                  {$this->tblName}.sur_name
                )) as names
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }

  public function exist(string $email = null) : bool
  {
    if(isset($email) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.email = '{$email}'
              AND 
                {$this->tblName}.status = '1'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getAll(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.external_client_id,
                {$this->tblName}.name,
                {$this->tblName}.user_name,
                {$this->tblName}.client_password,
                {$this->tblName}.whatsapp,
                {$this->tblName}.email,
                {$this->tblName}.create_date
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
      
      return $this->connection()->rows($sql);
    }

    return false;
  }

  public static function formatClients(array $clients = null) 
  {
    $DemoPerClient = new DemoPerClient;
    $ServicePerClient = new ServicePerClient;
    
    return array_map(function($client) use($DemoPerClient,$ServicePerClient) {
      if($demo = $DemoPerClient->getDemo($client['client_id']))
      {
        $client['demo'] = $demo;
        $minutes = DemoPerClient::calculateLeftMinutes($demo['active_date']);
        
        $client['demo']['left'] = [
          'active' => DemoPerClient::isActive($demo['active_date']),
          'minutes' => $minutes,
          'percentaje' => $minutes * 100 / DemoPerClient::DEMO_DURATION_MINUTES
        ];
      } else {
        $client['demo'] = false;
      }
      
      if($service = $ServicePerClient->getService($client['client_id']))
      {
        unset($client['demo']);
        $client['service'] = $service;

        $days = ServicePerClient::calculateLeftDays($service['active_date'],$service['day']);
        $client['service']['left'] = [
          'active' => ServicePerClient::isActive($service['active_date'],$service['day']),
          'days' => $days,
          'percentaje' => $days && $service['day']  ? $days * 100 / $service['day'] : 0
        ];
      } else {
        $client['service'] = false;
      }

      return $client;
    },$clients);
  }

  public function _getAll(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      if($clients = $this->getAll($user_login_id))
      {
        return self::formatClients($clients);
      }
    }

    return false;
  }

  public function getClient(int $client_id = null) 
  {
    if(isset($client_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.name,
                {$this->tblName}.user_login_id,
                {$this->tblName}.user_name,
                {$this->tblName}.client_password,
                {$this->tblName}.email,
                {$this->tblName}.create_date
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.client_id = '{$client_id}'
              ORDER BY 
                {$this->tblName}.create_date
              DESC
              ";
      
      return $this->connection()->row($sql);
    }

    return false;
  }

  public static function getClientByName(string $user_name = null) 
  {
    if(!$user_name)
    {
      return false;
    }
    
    $client = (new self)->findRow("user_name = ?",$user_name);

    if(!$client)
    {
      return false;
    }

    $DemoPerClient = new DemoPerClient;
    $ServicePerClient = new ServicePerClient;

    if($demo = $DemoPerClient->getDemo($client['client_id']))
    {
      $client['demo'] = $demo;
      
      $minutes = DemoPerClient::calculateLeftMinutes($demo['active_date']);
      $client['demo']['left'] = [
        'active' => DemoPerClient::isActive($demo['active_date']),
        'minutes' => $minutes,
        'percentaje' => $minutes * 100 / DemoPerClient::DEMO_DURATION_MINUTES
      ];
    } else {
      $client['demo'] = false;
    }
    
    if($service = $ServicePerClient->getService($client['client_id']))
    {
      unset($client['demo']);
      $client['service'] = $service;

      $days = ServicePerClient::calculateLeftDays($service['active_date'],$service['day']);
      $client['service']['left'] = [
        'active' => ServicePerClient::isActive($service['active_date'],$service['day']),
        'days' => $days,
        'percentaje' => $days * 100 / ServicePerClient::SERVICE_DURATION_DAYS
      ];
    } else {
      $client['service'] = false;
    }
    
    $client['sponsor']['names'] = (new UserData)->getName($client['user_login_id']);

    return $client;
  }
  
  public function getClients(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.name,
                {$this->tblName}.user_login_id,
                {$this->tblName}.user_name,
                {$this->tblName}.client_password,
                {$this->tblName}.email,
                {$this->tblName}.create_date
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ORDER BY 
                {$this->tblName}.create_date
              DESC
              ";
      
      return $this->connection()->rows($sql);
    }

    return false;
  }

  public static function _getActiveCounts(int $user_login_id = null) : int 
  {
    return (new Client)->getActiveCounts($user_login_id) ?? 0;
  }

  public function getActiveCounts(int $user_login_id = null) : int
  {
    if(isset($user_login_id) == true)
    {
      $sql = "SELECT
                COUNT({$this->tblName}.{$this->tblName}_id) as C
              FROM 
                {$this->tblName}
              INNER JOIN 
                service_per_client
              ON 
                service_per_client.client_id = {$this->tblName}.client_id
              AND 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '1'
              AND 
                service_per_client.status = '".ServicePerClient::IN_USE."'
              ";

      return $this->connection()->field($sql);
    }

    return 0;
  }
  
  public function getDemoCount(int $user_login_id = null) : int
  {
    if(!isset($user_login_id))
    {
      return false;
    }
    
    return $this->connection()->field("SELECT
      COUNT({$this->tblName}.{$this->tblName}_id) as c
    FROM 
      {$this->tblName}
    INNER JOIN 
      demo_per_client
    ON 
      demo_per_client.client_id = {$this->tblName}.client_id
    AND 
      {$this->tblName}.user_login_id = '{$user_login_id}'
    AND 
      {$this->tblName}.status != '1'
    ");

  }
  public function getServiceCount(int $user_login_id = null) : int
  {
    if(!isset($user_login_id))
    {
      return false;
    }
    
    return $this->connection()->field("
      SELECT
        COUNT({$this->tblName}.{$this->tblName}_id) as c
      FROM 
        {$this->tblName}
      INNER JOIN 
        service_per_client
      ON 
        service_per_client.client_id = {$this->tblName}.client_id
      AND 
        {$this->tblName}.user_login_id = '{$user_login_id}'
      AND 
        {$this->tblName}.status != '1'
    ");
  }

  public function getAllForAdmin() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.name,
              {$this->tblName}.user_login_id,
              {$this->tblName}.user_name,
              {$this->tblName}.client_password,
              {$this->tblName}.whatsapp,
              {$this->tblName}.email,
              {$this->tblName}.create_date
            FROM 
              {$this->tblName}
            ORDER BY 
              {$this->tblName}.create_date
            DESC
            ";
    
    return $this->connection()->rows($sql);
  }

  public function _getAllForAdmin() 
  {
    if($clients = $this->getAllForAdmin())
    {
      $DemoPerClient = new DemoPerClient;
      $ServicePerClient = new ServicePerClient;
      
      return array_map(function($client) use($DemoPerClient,$ServicePerClient) {
        if($demo = $DemoPerClient->getDemo($client['client_id']))
        {
          $client['demo'] = $demo;
          
          $minutes = DemoPerClient::calculateLeftMinutes($demo['active_date']);
          $client['demo']['left'] = [
            'active' => DemoPerClient::isActive($demo['active_date']),
            'minutes' => $minutes,
            'percentaje' => $minutes * 100 / DemoPerClient::DEMO_DURATION_MINUTES
          ];
        } else {
          $client['demo'] = false;
        }
        
        if($service = $ServicePerClient->getService($client['client_id']))
        {
          unset($client['demo']);
          $client['service'] = $service;

          $days = ServicePerClient::calculateLeftDays($service['active_date'],$service['day']);
          $client['service']['left'] = [
            'active' => ServicePerClient::isActive($service['active_date'],$service['day']),
            'days' => $days,
            'percentaje' => $days * 100 / ServicePerClient::SERVICE_DURATION_DAYS
          ];
        } else {
          $client['service'] = false;
        }
        
        $client['sponsor']['names'] = (new UserData)->getName($client['user_login_id']);

        return $client;
      },$clients);
    }

    return false;
  }

  public static function sendWhatsAppForExpiration(array $data = null)
  {
      return ApiWhatsApp::sendWhatsAppMessage([
          'message' => $data['message'],
          'image' => null,
          'contact' => [
              "phone" => $data['whatsApp'],
              "name" => $data['name']
          ]
      ]);
  }
  
  
  public static function getAdminWhatsApp(string $message = null)
  {
    $phones = ['5213317361196','573503637342'];

    return $phones[rand(0,sizeof($phones)-1)];
  }

  public static function requestDemoWhatsApp(string $message = null)
  {
      return ApiWhatsApp::sendWhatsAppMessage([
          'message' => $message,
          'image' => null,
          'contact' => [
              "phone" => self::getAdminWhatsApp(),
              "name" => 'Admin'
          ]
      ]);
  }
  
  public static function requestServiceWhatsApp(string $message = null)
  {
      return ApiWhatsApp::sendWhatsAppMessage([
          'message' => $message,
          'image' => null,
          'contact' => [
              "phone" => self::getAdminWhatsApp(),
              "name" => 'Admin'
          ]
      ]);
  }
}