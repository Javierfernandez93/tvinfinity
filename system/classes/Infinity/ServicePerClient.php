<?php

namespace Infinity;

use HCStudio\Orm;
use HCStudio\Util;

use Infinity\CreditPerUser;
use Infinity\Cout;
use Infinity\ApiInfinity;
use Infinity\Client;

class ServicePerClient extends Orm {
  protected $tblName  = 'service_per_client';

  /* CONSTANTS */
  const DELETE = -1;
  const FOR_ACTIVATE = 0;
  const IN_USE = 1;
  const EXPIRED = 2;
  const EXPIRED_FOR_RENOVATION = 3;
  const SERVICE_DURATION_DAYS = 30;
  const DEFAULT_CONNECTIONS = 3;

  const REQUEST_RENOVATION = 1;
  const DEFAULT_PACKAGE = 2;
  const RENOVATION_SENT = 0;
  const MIN_SERVICES_DAYS = 3;
  const DAYS_FOR_RENOVATION = 2;

  public function __construct() {
    parent::__construct();
  }
  
  public static function setAsRenovated(int $service_per_client_id = null) : bool
  {
    if(isset($service_per_client_id) == true) 
    {
      $ServicePerClient = new ServicePerClient;
      
      if($ServicePerClient->loadWhere('service_per_client_id = ?',$service_per_client_id))
      {
        $ServicePerClientNew = new ServicePerClient;

        $day = date("t");

        $ServicePerClientNew->client_id = $ServicePerClient->client_id;
        $ServicePerClientNew->connection = $ServicePerClient->connection;
        $ServicePerClientNew->month = $ServicePerClient->month;
        $ServicePerClientNew->adult = $ServicePerClient->adult;
        $ServicePerClientNew->create_date = time();
        $ServicePerClientNew->expiration = strtotime("+{$day} days");
        $ServicePerClientNew->day = $day;
        $ServicePerClientNew->request_renovation = self::RENOVATION_SENT;
        $ServicePerClientNew->autorenew = $ServicePerClient->autorenew;
        $ServicePerClientNew->active_date = time();
        $ServicePerClientNew->status = self::IN_USE;

        if($ServicePerClientNew->save())
        {
          return self::expireServiceForRenovation($ServicePerClient->getId());
        }
      }
    }

    return false;
  }

  public static function requestRenovation(int $client_id = null,int $user_login_id = null) : bool
  {
    if(isset($client_id,$user_login_id) == true) 
    {
      $ServicePerClient = new ServicePerClient;
      
      // @todo
      if($service_per_client_id = $ServicePerClient->getLastServiceId($client_id))
      {
        if($ServicePerClient->loadWhere('service_per_client_id = ?', $service_per_client_id))
        {
          $Client = new Client;
          
          if($Client->loadWhere('client_id = ?',$client_id))
          {
            $credit = $ServicePerClient->month;
            $package_id = self::getPackageId($ServicePerClient->adult,$ServicePerClient->month);
            
            if(CreditPerUser::hasCredits($user_login_id,$credit))
            {
              if($response = ApiInfinity::getRenovation($Client->external_client_id,$package_id))
              {
                if($response['s'] == 1)
                {
                  if(CreditPerUser::restCredits($user_login_id,$credit))
                  {
                    return self::setAsRenovated($service_per_client_id);
                  }
                }
              }
            }
          }
        }
      }
    }

    return false;
  }

  public static function expireService(int $service_per_client_id = null) : bool
  {
    if(isset($service_per_client_id) == true) 
    {
      $ServicePerClient = new ServicePerClient;

      if($ServicePerClient->loadWhere('service_per_client_id = ?',$service_per_client_id))
      {
        $ServicePerClient->status = self::EXPIRED;
    
        return $ServicePerClient->save();
      }
    }

    return false;
  }

  public static function expireServiceForRenovation(int $service_per_client_id = null) : bool
  {
    if(isset($service_per_client_id) == true) 
    {
      $ServicePerClient = new ServicePerClient;

      if($ServicePerClient->loadWhere('service_per_client_id = ?',$service_per_client_id))
      {
        $ServicePerClient->request_renovation = 0;
        $ServicePerClient->status = self::EXPIRED_FOR_RENOVATION;
    
        return $ServicePerClient->save();
      }
    }

    return false;
  }

  public static function setUpService(array $data = null) : bool
  {
    $ServicePerClient = new ServicePerClient;

    if($ServicePerClient->loadWhere('client_id = ?',$data['client_id']))
    {
      $ServicePerClient->active_date = time();
      $ServicePerClient->status = self::IN_USE;
  
      return $ServicePerClient->save();
    }

    return false;
  }

  public static function add(array $data = null,bool $full = false)
  {
    $ServicePerClient = new ServicePerClient;

    DemoPerClient::expireDemo($data['client_id']);

    $ServicePerClient->client_id = $data['client_id'];
    $ServicePerClient->connection = $data['connection'] ?? self::DEFAULT_CONNECTIONS;
    
    $ServicePerClient->month = $data['month'] ?? 1;
    $ServicePerClient->adult = 1;

    $ServicePerClient->request_renovation = 0;
    $ServicePerClient->autorenew = 0;

    if($full)
    {
      $day = date('t');
      $ServicePerClient->day = $day;
      $ServicePerClient->status = self::IN_USE;
      $ServicePerClient->active_date = time();
      $ServicePerClient->expiration = strtotime("+{$day} days");
    } else {
      $ServicePerClient->status = self::FOR_ACTIVATE;
    }

    $ServicePerClient->create_date = time();

    if($ServicePerClient->save())
    {
      if(CreditPerUser::restCredits($data['user_login_id'],$ServicePerClient->month))
      {
        return $ServicePerClient->getId();
      }
    }
  }

  public function hasDemo(int $client_id = null) : bool
  {
    if(isset($client_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.client_id = '{$client_id}'
              AND 
                {$this->tblName}.status IN(".self::IN_USE.",".self::FOR_ACTIVATE.")
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

	public function getService(int $client_id = null)
	{
	  if(isset($client_id) === true)
	  {
      $sql = "SELECT
            {$this->tblName}.{$this->tblName}_id,
            {$this->tblName}.adult,
            {$this->tblName}.connection,
            {$this->tblName}.day,
            {$this->tblName}.autorenew,
            {$this->tblName}.expiration,
            {$this->tblName}.status,
            {$this->tblName}.active_date,
            {$this->tblName}.request_renovation,
            {$this->tblName}.create_date
          FROM 
            {$this->tblName}
          WHERE 
            {$this->tblName}.client_id = '{$client_id}'
          AND 
            {$this->tblName}.status NOT IN(".self::DELETE.",".self::EXPIRED_FOR_RENOVATION.")
          ";
          
      return $this->connection()->row($sql);
	  }
  
	  return false;
	}

  public static function calculateLeftDays(int $active_date = null,int $day = null) 
  {
    $endSuscription = strtotime("+".$day." days", $active_date);

    return round(($endSuscription- time()) / (60 * 60 * 24));
  }

  public static function isActive(int $active_date = null,int $day = null) 
  {
    return self::calculateLeftDays($active_date,$day) > 0;
  }

  public static function sendService(int $service_per_client_id = null) 
  {
    $ServicePerClient = new ServicePerClient;
    
    if($ServicePerClient->loadWhere('service_per_client_id = ?',$service_per_client_id))
    {
      $Client = new Client;
      
      if($Client->loadWhere('client_id = ?',$ServicePerClient->client_id))
      {
        $Client->user_name = $Client->user_name ? $Client->user_name : Client::generateUserName($Client->name);
        $Client->user_name = Util::sanitizeString($Client->user_name, true, true);
        $Client->client_password = $Client->user_name;

        $package_id = self::getPackageId($ServicePerClient->adult,$ServicePerClient->month);

        if($client = ApiInfinity::generateService($Client->user_name,$Client->client_password,$package_id))
        {
          $Client->external_client_id = $client['user']['ID'];
          $Client->client_password = $client['user']['PASSWORD'];

          if($Client->save())
          {
            $ServicePerClient->expiration = strtotime($client['user']['EXPIRATION']);
            $ServicePerClient->day = date("t");
            $ServicePerClient->active_date = time();
            $ServicePerClient->status = self::IN_USE;

            if($ServicePerClient->save())
            {
              return self::sendServiceCredentials($Client->data());
            }
          }
        }
      }
    }

    return false;
  }

  public static function sendServiceCredentials(array $data = null) 
  {
    return ApiWhatsApp::sendWhatsAppMessage([
        'message' => ApiWhatsAppMessages::getIptvSetUpMessage(),
        'image' => null,
        'contact' => [
            "phone" => $data['whatsapp'],
            "name" => trim($data['name']),
            "user_name" => trim($data['user_name']),
            "client_password" => $data['client_password']
        ]
    ]);
  }

	public function getAllServices(int $status = null)
	{
	  if(isset($status) === true)
	  {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.adult,
                {$this->tblName}.connection,
                {$this->tblName}.day,
                {$this->tblName}.client_id,
                {$this->tblName}.autorenew,
                {$this->tblName}.status,
                {$this->tblName}.active_date,
                {$this->tblName}.create_date,
                client.user_login_id
              FROM 
                {$this->tblName}
              LEFT JOIN 
                client
              ON 
                client.client_id = {$this->tblName}.client_id
              WHERE 
                {$this->tblName}.status = '{$status}'
          ";
          
      return $this->connection()->rows($sql);
	  }
  
	  return false;
	}
	
  public function getAllServicesBySeller(int $user_login_id = null)
  {
    if(isset($user_login_id) === true)
	  {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.adult,
                {$this->tblName}.connection,
                {$this->tblName}.status,
                {$this->tblName}.day,
                client.name,
                {$this->tblName}.active_date,
                {$this->tblName}.create_date
              FROM 
                {$this->tblName}
              LEFT JOIN 
                client 
              ON 
                client.client_id = {$this->tblName}.client_id
              WHERE 
                {$this->tblName}.status = '".self::IN_USE."'
              AND 
                client.user_login_id = '{$user_login_id}'
              ";

      return $this->connection()->rows($sql);
	  }
  
	  return false;
  }

  public function hasServiceInCout(int $client_id = null,array $date = null) : bool
  {
    if(isset($client_id,$date) === true)
	  {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.client_id = '{$client_id}'
              AND 
                {$this->tblName}.status !=  '".self::DELETE."'
              AND 
                {$this->tblName}.create_date
              BETWEEN 
                '{$date['start_date']}'
              AND 
                '{$date['end_date']}'
              ";

      return $this->connection()->field($sql);
	  }
  
	  return false;
  }

  
  public function getServiceIdInDates(int $client_id = null,array $date = null) 
  {
    if(isset($client_id,$date) === true)
	  {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.client_id = '{$client_id}'
              AND 
                {$this->tblName}.status !=  '".self::DELETE."'
              AND 
                {$this->tblName}.create_date
              BETWEEN 
                '{$date['start_date']}'
              AND 
                '{$date['end_date']}'
              ";

      return $this->connection()->field($sql);
	  }
  
	  return false;
  }

  public function getAllServicesSold(int $user_login_id = null) : array|bool
	{
	  if(isset($user_login_id) === true)
	  {
      if($date = (new Cout)->getActual(true))
      {
        $Client = new Client;

        if($clients = $Client->getClients($user_login_id))
        {
          return array_filter($clients,function($client) use($date) {
              $service_per_client_id = $this->getServiceIdInDates($client['client_id'],$date);
              $client['service_per_client_id'] = $service_per_client_id;

              return $service_per_client_id ? true : false;
          });
        }
      }
	  }
  
	  return false;
	}

  public function getAllServicesSoldForComissions(int $user_login_id = null) : array|bool
	{
	  if(isset($user_login_id) === true)
	  {
      if($date = (new Cout)->getActual(true))
      {
        $Client = new Client;

        if($clients = $Client->getClients($user_login_id))
        {
          return array_filter(array_map(function($client) use($date) {
            
            if($service_per_client_id = $this->getServiceIdInDates($client['client_id'],$date))
            {
              $client['service_per_client_id'] = $service_per_client_id;
            }

            return $client;
          },$clients),function($client){
              return isset($client['service_per_client_id']);
          });
        }
      }
	  }
  
	  return false;
	}

  public static function includeDaysForServices(array $services = null) 
  {
    return array_map(function($service){
      $left_days = self::calculateLeftDays($service['active_date'],$service['day']);
      $service['left_days'] = $left_days;
      $service['aviable_to_alert'] = $left_days <= self::MIN_SERVICES_DAYS;

      return $service;
    },$services);
  }

  public static function getServicesForSoonExpiration() 
  {
    if($users = (new LicencePerUser)->getAllLicences(LicencePerUser::USED))
    {
      $UserContact = new UserContact;
      $UserData = new UserData;

      return array_map(function($user) use($UserContact,$UserData) {
        $user['whatsApp'] = $UserContact->getWhatsApp($user['user_login_id_to'] ?? null);
        $user['name'] = $UserData->getName($user['user_login_id_to'] ?? null);

        $services = (new ServicePerClient)->getAllServicesBySeller($user['user_login_id_to'] ?? null);

        if($services)
        {
          $user['clients'] = self::includeDaysForServices($services);
        }

        return $user;
      },$users);
    }

    return false;
  }

  public static function getClientsForSoonExpiration(int $user_login_id = null) 
  {
    return (new self)->getAllServicesSold($user_login_id);
  }

  public static function setAutorenewByState(int $client_id = null,bool $autorenew = null) 
  {
    if(isset($client_id,$autorenew) == true) 
    {
      $ServicePerClient = new ServicePerClient;
      
      if($ServicePerClient->loadWhere('client_id = ?',$client_id))
      { 
        $ServicePerClient->autorenew = (int)filter_var($autorenew, FILTER_VALIDATE_BOOLEAN);

        return $ServicePerClient->save();
      }
    }

    return false;
  }

  public static function disableAutoRenew(int $client_id = null) 
  {
    if(isset($client_id) == true) 
    {
      return self::setAutorenewByState($client_id,false);
    }

    return false;
  }
  
  public static function enableAutoRenew(int $client_id = null) 
  {
    if(isset($client_id) == true) 
    {
      return self::setAutorenewByState($client_id,true);
    }

    return false;
  }
  
  /* @package_id = 
    2 Oficial 1 Mes Completo, 
    3 = Oficial 1 Mes Sin XXX
    4 = Official 3 Meses Sin XXX
    5 = Official 3 Meses Completo
    10 = 6 meses completo
    11 = 6 meses sin xxx
    12 = 12 meses completo
    13 = 13 meses sin xxx
  */
  public static function getPackageId(bool $adult = null,int $month = null)
  { 
    // return 148; // 3x1
    return 135; // 2x1
    return 116; // 1 month

    if($adult)
    {
      if($month == 1)
      {
        return 116;
      } else if($month == 3) {
        return 81;
      } else if($month == 6) {
        return 84;
      } else if($month == 12) {
        return 87;
      }
    } else {
      if($month == 1)
      {
        return 3;
      } else if($month == 3) {
        return 4;
      } else if($month == 6) {
        return 11;
      } else if($month == 12) {
        return 87;
      }
    }

    return 2;
  }

  public function getLastServiceId(int $client_id = null)
	{
	  if(isset($client_id) === true)
	  {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.client_id = '{$client_id}'
              AND 
                {$this->tblName}.status != '".self::DELETE."'
              ORDER BY 
                {$this->tblName}.create_date
              DESC 
          ";
          
      return $this->connection()->field($sql);
	  }
  
	  return false;
	}

  public static function requestFull(array $data = null)
  {
    if(self::add($data,true))
    {
      $Client = new Client;

      if($Client->loadWhere('client_id = ?',$data['client_id']))
      {
        $data['package_id'] = $data['package_id'] ?? self::DEFAULT_PACKAGE;

        if($response = ApiInfinity::requestFull($Client->external_client_id,$data['package_id']))
        {
          return self::sendServiceCredentials($Client->data());
        }
      }
    }

    return false;
  }

  public static function sendDemoToServiceCredentials(array $data = null) 
  {
    return ApiWhatsApp::sendWhatsAppMessage([
        'message' => ApiWhatsAppMessages::getIptvDemoToServiceSetUpMessage(),
        'image' => null,
        'contact' => [
            "phone" => $data['whatsapp'],
            "name" => trim($data['name']),
            "user_name" => $data['user_name'],
            "client_password" => $data['client_password']
        ]
    ]);
  }

  public function getAllForAutorenovation() : array|bool
  {
    if($services = $this->_getAllForAutorenovation())
    {
      return array_filter(array_map(function($service){
        $service['days_for_expire'] = self::calculateLeftDays($service['active_date'],$service['day']);

        return $service;
      },$services),function($service){
        return $service['days_for_expire'] > 0 && $service['days_for_expire'] <= self::DAYS_FOR_RENOVATION;
      });
    }

    return false;
  }

  public function _getAllForAutorenovation()
	{
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.active_date,
              {$this->tblName}.day,
              client.user_login_id,
              client.client_id,
              client.external_client_id,
              client.user_name
            FROM 
              {$this->tblName}
            LEFT JOIN
              client 
            ON 
              client.client_id = {$this->tblName}.client_id
            WHERE 
              {$this->tblName}.autorenew = '1'
            AND 
              {$this->tblName}.status = '".self::IN_USE."'
        ";
        
    return $this->connection()->rows($sql);
	}
}