<?php

namespace Infinity;

use HCStudio\Orm;
use JFStudio\Constants;

use Infinity\CatalogLevel;

class UserReferral extends Orm {
  protected $tblName  = 'user_referral';

  const WAITING_FOR_PAYMENT = 0;
  const ON_MATRIZ = 1;
  public function __construct() {
    parent::__construct();
  }
  
  public function getLastReferrals(int $referral_id = null) 
  {
    return $this->getReferrals($referral_id," ORDER BY {$this->tblName}.create_date DESC LIMIT 5 ");
  }

  public function getReferralCount(int $referral_id = null,string $filter = '') 
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                COUNT({$this->tblName}.user_login_id) as c
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.status != '".Constants::DELETE."'
                {$filter}
              ";
              
      return $this->connection()->field($sql);
    }
  }
  
  public function getReferralId(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.referral_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.",".self::WAITING_FOR_PAYMENT.")
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getNextPosition(int $referral_id = null) 
  {
    return $this->getLastPosition($referral_id) + 1;
  }

  public function getLastPosition(int $referral_id = null) : int 
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.position
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ORDER BY 
                {$this->tblName}.position
              DESC 
              LIMIT 1
              ";

      if($last_position = $this->connection()->field($sql))
      {
        return $last_position;
      }
    }

    return 0;
  }

  public static function setOnMatriz(int $user_login_id = null) : bool
  {
    if(isset($user_login_id) === true) 
    {
      $UserReferral = new UserReferral;

      if($UserReferral->isWaitingForSetOnMatriz($user_login_id))
      {
        if($referral_id = $UserReferral->getReferralId($user_login_id))
        {
          if($catalog_level_id = $UserReferral->getCatalogLevelIdForSignup($referral_id))
          { 
            if($UserReferral->loadWhere('referral_id = ? AND user_login_id = ?',[$referral_id,$user_login_id]))
            {
              $position = $UserReferral->getNextPosition($referral_id);

              $UserReferral->leak_id = $UserReferral->getNextLeak($referral_id,$position);
              $UserReferral->position = $position;
              $UserReferral->catalog_level_id = $catalog_level_id;
              $UserReferral->status = self::ON_MATRIZ;

              return  $UserReferral->save();
            }
          }
        }
      }
    }
    
    return false;
  }

  public function isWaitingForSetOnMatriz(int $user_login_id = null) : bool
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.referral_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.status IN (".self::WAITING_FOR_PAYMENT.")
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getUserReferralId(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.referral_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";

      return $this->connection()->field($sql);
    }
  }

  public function getLeakUserReferralIdByLevel(int $user_login_id = null,int $amountOfLevel = null) 
  {
    if(isset($user_login_id) === true) 
    {
      if($sponsors = $this->_getLeakUserReferralIdByLevel($user_login_id,$amountOfLevel))
      {
        return $sponsors[sizeof($sponsors)-1];
      }
    }

    return false;
  }
 
  public function _getLeakUserReferralIdByLevel(int $user_login_id = null,int $amountOfLevel = null) 
  {
    if(isset($user_login_id) === true) 
    {
      $level = 0;

      do {
        $level++;
        $user_login_id = $this->getLeakId($user_login_id);

        if($user_login_id)
        {
          $sponsors[] = $user_login_id;
        }
      } while($level < $amountOfLevel);

      return $sponsors;
    }

    return false;
  }
  
  public function getLeakId(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      if($leak_id = $this->_getLeakId($user_login_id))
      {
        return $leak_id;
      }

      if($referral_id = $this->getReferralId($user_login_id))
      {
        return $referral_id;
      }
    }
  }
  
  public function _getLeakId(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.leak_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";

      return $this->connection()->field($sql);
    }
  }

  public function getNextLeak(int $referral_id = null,int $position = null) 
  {
    if(isset($referral_id,$position) === true) 
    {
      if($position > 7)
      {
        $leak_id = $position % 7;
        $leak_id = $leak_id == 0 ? 7 : $leak_id;

        return $this->getUserIdByReferralAndPosition($referral_id,$leak_id);
      }
    }

    return 0;
  }

  public function getLastLeak(int $referral_id = null) : int
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.position
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.leak_id != '0' 
              AND 
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ORDER BY 
                {$this->tblName}.position 
              DESC 
              ";

      if($position = $this->connection()->field($sql))
      {
        return $position;
      }
    }

    return 0;
  }
  
  public function getUserIdByReferralAndPosition(int $referral_id = null,int $position = null) 
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.position = '{$position}' 
              AND 
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getCatalogLevelIdForSignup(int $referral_id = null) 
  {
    if(isset($referral_id) === true) 
    {
      if($catalog_level_id = $this->_getLastLevelUnlocked($referral_id))
      {
        $amountOfReferrals = $this->getAmountOfReferredOnCatalogLevelId($referral_id,$catalog_level_id);
        $maxReferralsAmount = (new CatalogLevel)->getAmountOfReferralsById($catalog_level_id);

        if($amountOfReferrals == $maxReferralsAmount)
        {
          $catalog_level_id = (new CatalogLevel)->getLevelById($catalog_level_id+1);
        }
      }

      return $catalog_level_id;
    }
  }

  public function _getLastLevelUnlocked(int $referral_id = null) 
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.catalog_level_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ORDER BY 
                catalog_level_id 
              DESC 
              LIMIT 1
              ";
      if($catalog_level_id = $this->connection()->field($sql))
      {
        return $catalog_level_id;
      }
    }

    return CatalogLevel::DEFAULT_CATALOG_LEVEL_ID;
  }
  
  public function getAmountOfReferredOnCatalogLevelId(int $referral_id = null,int $catalog_level_id = null)  
  {
    if(isset($referral_id,$catalog_level_id) === true) 
    {
      $sql = "SELECT 
                COUNT({$this->tblName}.{$this->tblName}_id) as c
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.catalog_level_id = '{$catalog_level_id}' 
              AND 
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";

      return $this->connection()->field($sql);
    }
  }

  public function getReferrals(int $referral_id = null,string $filter = '') 
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.user_login_id,
                user_data.names,
                user_address.country_id,
                user_account.image,
                user_login.signup_date,
                user_login.company_id,
                user_login.email,
                user_contact.phone
              FROM 
                {$this->tblName} 
              LEFT JOIN 
                user_data
              ON 
                user_data.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                user_account
              ON 
                user_account.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                user_login
              ON 
                user_login.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                user_address
              ON 
                user_address.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                user_contact
              ON 
                user_contact.user_login_id = {$this->tblName}.user_login_id
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.status IN (".self::WAITING_FOR_PAYMENT.",".Constants::AVIABLE.")
                {$filter}
              ";

      return $this->connection()->rows($sql);
    }
  }
  
  public function getReferralsIds(int $referral_id = null) 
  {
    if(isset($referral_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName} 
              WHERE 
                {$this->tblName}.referral_id = '{$referral_id}' 
              AND 
                {$this->tblName}.status IN (".self::WAITING_FOR_PAYMENT.",".Constants::AVIABLE.")
              ";

      return $this->connection()->column($sql);
    }
  }
  
  public function getReferral(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                user_login.user_login_id,
                user_data.names,
                user_account.image,
                user_login.signup_date,
                user_login.email
              FROM 
                {$this->tblName} 
              LEFT JOIN 
                user_data
              ON 
                user_data.user_login_id = {$this->tblName}.referral_id
              LEFT JOIN 
                user_account
              ON 
                user_account.user_login_id = {$this->tblName}.referral_id
              LEFT JOIN 
                user_login
              ON 
                user_login.user_login_id = {$this->tblName}.referral_id
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}' 
              AND 
                {$this->tblName}.status != '".Constants::DELETE."'
              GROUP BY 
                {$this->tblName}.user_login_id
              ";
              
      return $this->connection()->row($sql);
    }
  }

  public function getNetwork(int $limit = -1 ,string $referral_id = null,int $count = 0) 
  {
    $result = [];
        
    $sql = "SELECT user_login_id FROM user_referral WHERE referral_id IN ({$referral_id})";      

    if (($count != $limit) && ($data = $this->connection()->column($sql))) {
      $count++;
      $join = join(",", $data);
      $result = $this->getNetwork($limit, $join, $count);
      $result = array_merge(array($data), $result);
    }

    return $result;
  }

  public function getSponsorByReverseLevel(int $limit = -1 ,string $user_login_id = null,int $count = 0) : array
  {
		if($network = $this->getNetworkReverse($limit,$user_login_id))
    {
      return self::simplyfyNetwork($network);
    }

    return [];
  }
   
  public function simplyfyNetwork(array $network = null) : array {
    $data = [];

    foreach($network as $level)
    {
      foreach($level as $user_login_id)
      {
        $data[] = $user_login_id;
      }
    }

    return $data;
  } 

  public function getNetworkReverse(int $limit = -1 ,string $user_login_id = null,int $count = 0) 
  {
    $result = [];
 
    $sql = "SELECT referral_id FROM user_referral WHERE user_login_id IN ({$user_login_id})";      

    if (($count != $limit) && ($data = $this->connection()->column($sql))) {
      $count++;
      $join = join(",", $data);
      $result = $this->getNetworkReverse($limit, $join, $count);
      $result = array_merge(array($data), $result);
    }

    return $result;
  }

}