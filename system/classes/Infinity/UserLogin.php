<?php

namespace Infinity;

use HCStudio\Orm;
use HCStudio\Session;
use HCStudio\Token;
use HCStudio\Util;
use HCStudio\Connection;

use World\Country;

use Infinity\AdviceType;
use Infinity\BuyPerUser;
use Infinity\UserPlan;
use Infinity\TransactionRequirementPerUser;

class UserLogin extends Orm {
  const DELETED = -1;
  protected $tblName  = 'user_login';
  private $Session = false;
  private $Token   = false;
  
  public $_data = [];
  public $_parent = [];
  public $_parent_id = false;
  public $_save_class = false;
  public $logged  = false;
  private $field_control = 'password';
  private $field_session = 'email';
  private $_field_type = 'email';

  /* signup */
  const SIGNUP_DAYS = 7;
  const REFERRAL_PATH = 'apps/signup/?uid=';
  public function __construct($save_class = false, $autoLoad = true,$redir = true) {
    parent::__construct();
    
    $this->_save_class = $save_class;
    
    $this->Session = new Session($this->tblName);
    $this->Token = new Token;

    if($autoLoad)
    {
      if($this->logoutRequest()) return false ;

      if($this->checkCS() && !$this->loginRequest())
      {
        if($this->isValidPid())
        {
          $this->login($this->Token->params[$this->field_session],$this->Token->params[$this->field_control]);
        }
      } else if($this->loginRequestExternal() === true) {
        $this->loginExternal();
      } else if($this->loginRequest()) {
        $this->login();
      } else if($this->hasPidRequest() === true) {
        $this->loginWithPid();
      }
    }
  }
  
  public function hasPidRequest()
  {
    return isset($_GET['pid']) === true && is_array($_GET['pid']) ? true : false;
  }
  
  public function loginWithPid()
  {
    if($this->Token->checkToken($_GET['pid']) == true)
    {
      $this->login($this->Token->params[$this->field_session], $this->Token->params['password']);
    }
  }

  public function hasPermission($permission = null) : bool
  {
    if($this->getId())
    {
      if(isset($permission) === true)
      {
        $PermissionPerUserSupport = new PermissionPerUserSupport;
        
        return $PermissionPerUserSupport->_hasPermission($this->getId(),$permission);
      }
    }
  }
  
  public function checkCS() {
    return $this->Session->get('pid');
  }

  public function setFieldSession($field_session = false) {
    $this->_setFieldSession($field_session);
  }

  public function _setFieldSession($field_session = false) {
    if($field_session) $this->field_session = $field_session;
  }

  public function getFieldSession() {
    return ['fieldsession'=>$this->field_session,'field_type'=>$this->_field_type];
  }

  public function logoutRequest(bool $logout = null) {
    $logout = ($logout) ? $logout : Util::getVarFromPGS('logout');

    if($logout) return $this->logout();
  }

  public function deleteSession() {
    $this->Session->destroy();
  }
  public function isAbiableToSingUp()
  {
    if(!$this->logged)
      if($this->getId() === 0)
        if(!$this->_data && !$this->_parent)
          return true;

    return false;
  }

  public function getDataForSignupExternal()
  {
    if($this->logged === true)
    {
      return [
        'email' => $this->email,
        'password' => $this->password,
        'names' => $this->_data['user_data']['names'],
        'image' => $this->_data['user_account']['image'],
        'phone' => $this->_data['user_contact']['phone'],
        'country_id' => $this->_data['user_address']['country_id'],
      ];
    }

    return false;
  }

  public function getCountryId()
  {
    if($this->getId())
    {
      return $this->_data['user_address']['country_id'] ? $this->_data['user_address']['country_id'] : 159;// default mx
    }
  }
  
  public function logout($reload = true)
  {
    $this->deleteSession();
    if($reload) header("Refresh: 0;url=./index.php");
  }

  /* starts security */
  public function getPassForUser() {
    return $this->isAbiableSalt((new Token())->randomKey(10));
  }

  private function getUniqueSalt() {
    if($salt = $this->isAbiableSalt((new Token())->randomKey(5))) return $salt;

    $this->getUniqueSalt();
  }

  private function isAbiableSalt($salt) {
    $sql = "SELECT {$this->tblName}.salt FROM {$this->tblName} WHERE {$this->tblName}.salt = '{$salt}'";

    if($this->connection()->field($sql)) return false;

    return $salt;
  }

  public function isUserOnline($company_id = false) {
    if($company_id)
    {
      $sql = "SELECT {$this->tblName}.last_login_date FROM {$this->tblName} WHERE {$this->tblName}.company_id = '{$company_id}'";
      return $this->isOnline($this->connection()->field($sql));
    }

    return false;
  }

  public function isOnline($last_login_date = false)
  {
    if($last_login_date >= strtotime("-5 minutes")) return true;

    return false;
  }

  public function getData($company_id = false,$filter = '')  {
    if($company_id)
    {
      $sql = "SELECT
                {$this->tblName}.company_id,
                user_data.names,
                {$this->tblName}.signup_date,
                {$this->tblName}.last_login_date
              FROM
                {$this->tblName}
              LEFT JOIN
                user_data
              ON
                user_data.user_login_id = {$this->tblName}.company_id
              WHERE
                {$this->tblName}.company_id = {$company_id}
              AND
                {$this->tblName}.status = '1'
                {$filter}";

      return $this->connection()->row($sql);
    }
    return false;
  }

  public function getCompanyIdByMail(string $email = null) 
  {
    if(isset($email) === true)
    {
      $sql = "SELECT
                {$this->tblName}.company_id
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.email = '{$email}'";

      return $this->connection()->field($sql);
    }
    return false;
  }

  private function needChangeControlData() {
    if(!$this->last_login_date) return true;

    if(strtotime('+ '.$this->expiration_salt_date.' minutes',$this->last_login_date) < time()) return true;

    return false;
  }

  public function renewSalt() {
    $this->setSalt(true);
  }

  private function setSalt($force_to_set_salt = false) {
    if($this->needChangeControlData() || $force_to_set_salt)
    {
      $this->salt = $this->getUniqueSalt();
      $this->save();
    }
  }

  private function saveControlData() {
    if($this->needChangeControlData())
    {
      $this->ip_user_address = $_SERVER['REMOTE_ADDR'];
      $this->last_login_date = time();
      $this->save();
    }
  }

  private function doLogin() {
    if($this->hasLogged())
    {
      $this->setSalt();
      $this->setPid();
      $this->saveControlData();
      $this->loadProfile();
      $this->logged = true;
    }
    return $this->logged;
  }

  public function login($field_session = false,$field_control = false) 
  {
    $field_session = ($field_session) ? $field_session : Util::getVarFromPGS($this->field_session,false);
    $field_control = ($field_control) ? $field_control : sha1(Util::getVarFromPGS($this->field_control,false));
    
    if($this->cargarDonde("{$this->field_session}=? AND {$this->field_control}=?",[$field_session,$field_control]))
    {
      return $this->doLogin();
    }
  }
 
  public function loginExternal($field_session = false,$field_control = false) 
  {
    $field_session = ($field_session) ? $field_session : Util::getVarFromPGS($this->field_session,false);
    $field_control = ($field_control) ? $field_control : Util::getVarFromPGS($this->field_control,false);
    
    if($this->loadWhere("{$this->field_session}=? AND {$this->field_control}=?",[$field_session,$field_control]))
    {
      return $this->doLogin();
    }
  }

  public function createPid()
  {
    $data = [
      $this->field_session => $this->{$this->field_session},
      $this->field_control => $this->{$this->field_control},
      "securitySalt" => sha1($this->last_login . $this->ip_user_address . $this->salt),
    ];
    return $this->Token->getToken($data,true,true);
  }

  public function loadDataByClassName($ClassName,$var)
  {
    if($ClassName && $var)
    {
      if(!isset($this->_data[$var]))
      {
        $_parent_id = ($this->_parent_id) ? $this->_parent_id : $this->getId();

        $Class = new $ClassName();
        $Class->cargarDonde('user_login_id = ?',$_parent_id);

        if(!$Class->getId()) $Class->user_login_id = $_parent_id;

        $this->_data[$var] = $Class->atributos();

        if($this->_save_class) {
          if(!$Class->getId()) $Class->user_login_id = $this->getId();

          $this->_parent[$var] = $Class;
        }

        return true;
      }
    }
    return false;
  }

  public function loadProfile()
  {
    $this->loadDataByClassName(__NAMESPACE__.'\UserData','user_data');
    $this->loadDataByClassName(__NAMESPACE__.'\UserAddress','user_address');
    $this->loadDataByClassName(__NAMESPACE__.'\UserContact','user_contact');
    $this->loadDataByClassName(__NAMESPACE__.'\UserAccount','user_account');
  }
  public function getUniqueToken($lenght = 5, $field = 'secret', $table = 'user_login', $field_as = 'total')
  {
    if($token = $this->Token->randomKey($lenght))
    {
      $sql = "SELECT count({$table}.{$field}) as {$field_as} FROM {$table} WHERE {$table}.{$field} = '{$token}'";

      if($this->connection()->field($sql)) $this->getUniqueToken();
      else return $token;
    }

    return false;
  }

  public function setPid() {
    $pid = $this->createPid();
    $this->Session->set('pid',$pid);
  }

  public function hasLogged() {
    return ($this->getId() == 0) ? false : true;
  }

  public function loginFacebookRequest() {
    if(isset($_GET['user_key']) || isset($_POST['user_key']))
      return true;

    return false;
  }

  public function loginRequest() {
    
    if(isset($_GET[$this->field_session]) || isset($_POST[$this->field_session]))
    {
      if(isset($_GET[$this->field_control]) || isset($_POST[$this->field_control])) {
        return true;
      }
    }

    return false;
  }

  public function loginRequestExternal() {
    
    if(isset($_GET[$this->field_session]) || isset($_POST[$this->field_session]))
    {
      if(isset($_GET[$this->field_control]) || isset($_POST[$this->field_control])) 
      {
        if(isset($_GET['external']) || isset($_POST['external'])) {
          return true;
        }
      }
    }

    return false;
  }

  public function isValidPid() {
    $pid = $this->Session->get('pid');  
    return ($this->Token->checkToken($pid)) ? true : false;
  }

  public function isValidMail(string $email = null) {
    $sql = "
            SELECT 
              {$this->tblName}.email
            FROM 
              {$this->tblName}
            WHERE
              {$this->tblName}.email = '{$email}'
            ";

    return $this->connection()->field($sql) ? false : true;
  }

  public function hasData($data)
  {
    if(is_array($data))
    {
      foreach ($data as $key => $field)
        if(!isset($field) || empty($field)) return false;

    } else if(!$data || $data == "") return false;

    return true;
  }

  public function isUniqueMail($email = false) {
    $sql = "SELECT email FROM user_login WHERE user_login.email = '{$email}' LIMIT 1";
    return ($this->connection()->field($sql)) ? false : true;
  }

  public function doSignup(array $data = null) 
  {
    $UserLogin = new UserLogin;
    $UserLogin->email = $data['email'];
    $UserLogin->password = $data['encrypted'] ? $data['password'] : sha1($data['password']);
    $UserLogin->signup_date = time();
    
    if($UserLogin->save())
    {
      $UserLogin->company_id = $UserLogin->getId();
      
      if($UserLogin->save())
      {
        $UserData = new UserData;
        $UserData->user_login_id = $UserLogin->company_id;
        $UserData->names = trim($data['names']);
        
        if($UserData->save())
        {
          $UserContact = new UserContact;
          $UserContact->user_login_id = $UserLogin->company_id;
          $UserContact->phone = isset($data['phone']) ? $data['phone'] : '';
          
          if($UserContact->save())
          {
            $UserAddress = new UserAddress;
            $UserAddress->user_login_id = $UserLogin->company_id;
            $UserAddress->address = '';
            $UserAddress->colony = '';
            $UserAddress->city = '';
            $UserAddress->state = '';
            $UserAddress->country = '';
            $UserAddress->country_id = isset($data['country_id']) ? $data['country_id'] : '';
            
            if($UserAddress->save())
            {
              $UserAccount = new UserAccount;
              $UserAccount->user_login_id = $UserLogin->company_id;
              $UserAccount->image = UserAccount::DEFAULT_IMAGE;

              if(isset($data['referral']))
              {
                $UserReferral = new UserReferral;
                $UserReferral->referral_id = $data['referral']['user_login_id'];
                $UserReferral->user_login_id = $UserLogin->company_id;
                $UserReferral->catalog_level_id = 0;
                $UserReferral->status = UserReferral::WAITING_FOR_PAYMENT;
                $UserReferral->create_date = time();
                $UserReferral->save();
              }

              if($UserAccount->save())
              {
                return $UserLogin->company_id;
              }
            }
          }
        }
      }
    }

    return false;
  }

  public function getEmail(int $user_login_id = null) 
  {
    if (isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.email
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
      return $this->connection()->field($sql); 
    }

    return false;
  }

  public function getFirsNameLetter() 
  {
    if ($this->getId()) 
    {
      return mb_substr((new UserData)->getNames($this->company_id), 0, 1);
    }
  }

  public function getNames() 
  {
    if ($this->getId()) 
    {
      return ucfirst((new UserData)->getNames($this->company_id));
    }
  }

  public static function redirectTo(string $route_name = null)
  {
    Util::redirectTo(TO_ROOT."/apps/login/",[
      'page' => Util::getCurrentURL(),
      'route_name' => $route_name
    ]);
  }

  function checkRedirection()
  {
    // @todo
  }

  /* profile fun */  
  public function getPlan()
  {
    return (new UserPlan)->getPlan($this->company_id);
  }
  
  public function hasPlan() : bool
  {
    return (new UserPlan)->hasPlan($this->company_id);
  }

  public function hasCard() : bool
  { 
    return (new UserCard)->hasCard($this->company_id);
  }

  public function getLanding() : string 
  {
    if($this->getId())
    {
      return self::_getLanding($this->company_id);
    }
  }

  public static function _getLanding(int $user_login_id = null) : string 
  {
    if(isset($user_login_id) === true)
    {
      return Connection::getMainPath().'/apps/signup/?uid='.$user_login_id;
    }
  }

  public function getReferralId() : string 
  {
    if($this->getId())
    {
      return (new UserReferral)->getReferralId($this->company_id);
    }
  }

  /* profile fun */  
  public function getProfile(int $user_login_id = null)
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.email,
                {$this->tblName}.user_login_id,
                user_contact.phone,
                user_address.country_id,
                user_data.names,
                user_account.image
              FROM 
                {$this->tblName}
              LEFT JOIN
                user_data 
              ON 
                user_data.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN
                user_contact 
              ON 
                user_contact.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN
                user_address 
              ON 
                user_address.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN
                user_account 
              ON 
                user_account.user_login_id = {$this->tblName}.user_login_id
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
      
      return $this->connection()->row($sql);
    }
  }

  /* profile fun */  
  public function getReferralCount()
  {
    if($this->getId())
    {
      return (new UserReferral)->getReferralCount($this->company_id);
    }

    return 0;
  }
  
  public function getReferral()
  {
    if($this->getId())
    {
      return (new UserReferral)->getReferral($this->company_id);
    }

    return 0;
  }
  
  public function getLastTransactions()
  {
    if($this->getId())
    {
      return (new TransactionRequirementPerUser)->getLastTransactions($this->company_id,"LIMIT 5");
    }
  }

  public function getSignupDate(int $company_id = null)
  {
    if(isset($company_id))
    {
      $sql = "SELECT
                {$this->tblName}.signup_date
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$company_id}'";

      return $this->connection()->field($sql);
    }

    return 0;
  }
  
  public static function _isActive(int $company_id = null) : bool
  {
    return (new BuyPerUser)->isActive($company_id);
  }

  public function isActive() : bool
  {
    if($this->getId())
    {
      return self::_isActive($this->company_id);
    }

    return false;
  }

  public function getPid()
  {
    if(isset($this->Session)) {
      return $this->Session->get('pid');
    }

    return false;
  }

  public function getLastSigned()
  {
    if($users = $this->_getLastSigned())
    {
      $Country = new Country;

      return array_map(function($user) use($Country){
        $user['advice_type'] = AdviceType::SIGNUP;
        $user['showed'] = false;
        $user['country'] = $Country->getCountryName($user['country_id']);
        return $user;
      },$users);
    }

    return false;
  }

  public function _getLastSigned()
  {
    $minSignupDate = strtotime("-".self::SIGNUP_DAYS." days");
  
    $sql = "SELECT
              {$this->tblName}.signup_date,
              user_data.names,
              user_address.country_id
            FROM
              {$this->tblName}
            LEFT JOIN
              user_data
            ON 
              user_data.user_login_id = {$this->tblName}.user_login_id
            LEFT JOIN
              user_address
            ON 
              user_address.user_login_id = {$this->tblName}.user_login_id
            WHERE 
              {$this->tblName}.signup_date >= '{$minSignupDate}'";

    return $this->connection()->rows($sql);
  }

  public function getUserData(int $user_login_id = null)
  {
    if(!$this->getId())
    {
      return false;
    }

    return $this->_getUserData($this->company_id);
  }
  
  public function _getUserData(int $user_login_id = null)
  {
    if(!isset($user_login_id))
    {
      return false;
    }
    
    return $this->connection()->row("SELECT
      user_data.user_login_id,
      user_data.names,
      user_account.image
    FROM
      user_data
    LEFT JOIN
      user_account
    ON 
      user_account.user_login_id = user_data.user_login_id
    WHERE 
      user_data.user_login_id = '{$user_login_id}'
    ");
  }

  public function getBuysForAdvices()
  {
    return (new BuyPerUser)->getBuysForAdvices();
  }

  public function getPidQuery() 
  {
    if($this->logged === true)
    {
      return "?".http_build_query(["pid" => $this->getPid()]);
    }
  }

  public function getTimeZone()
  {
    if($this->hasTimeZoneConfigurated())
    {
      return (new UserAccount)->getTimeZone($this->company_id);
    }

    return UserAccount::DEFAULT_TIME_ZONE;
  }

  public function hasTimeZoneConfigurated()
  {
    if($this->logged === true)
    {
      return $this->_data['user_account']['catalog_timezone_id'] ? true : false;
    }
  }

  public function getReferralLanding()
  {
    if($this->logged === true)
    {
      return $this->_data['user_account']['landing'] ? $this->_data['user_account']['landing'] : self::REFERRAL_PATH.$this->company_id;
    }
  }

  public function isActiveOnPackage(int $package_id = null,int $additionalDays = 0) : bool
  {
    return $this->_isActiveOnPackage($package_id,$this->company_id,$additionalDays);
  }
  
  public function _isActiveOnPackage(int $package_id = null,int $user_login_id = null,int $additionalDays = 0) : bool
  {
    $active = false;
    
    if($buys = (new BuyPerUser)->getAll($user_login_id,"AND buy_per_user.status = '".BuyPerUser::VALIDATED."'"))
    {
      foreach($buys as $buy)
      {
        if($data = BuyPerUser::_unformatData($buy))
        {
          if(BuyPerUser::hasPackageOnItems($data['items'],$package_id))
          {
            $days = 30 + $additionalDays;

            $end_date = strtotime("+ ".$days." days", $buy['approved_date']);
            
            if(time() < $end_date)
            {
              $active = true;
              break;
            }
          }
        }
      }
    }

    return $active;
  }
  
  public function isReadyToDelete() : bool
  {
    return !$this->isActiveOnPackage(1,3);
  }

  public function disableAccount()
  {
    return $this->_disableAccount($this->company_id);
  }

  public function _disableAccount(int $user_login_id = null)
  {
    if(!$user_login_id)
    {
      return false;
    }

    $UserLogin = new self(false,false);
    
    if(!$UserLogin->loadWhere("user_login_id = ?",$user_login_id))
    {
      return false;
    }

    $UserLogin->status = -1;
    
    return $UserLogin->save();
  }
  /* api */
  public function getCredits() {
    if(!$this->getId())
    {
      return 0;
    }

    $credits = (new CreditPerUser)->findField("user_login_id = ? ",$this->company_id,"credit");

    if(!$credits)
    {
      return 0;
    }

    return $credits;
  }

  public function getNetwork(int $limit = 3) : array|bool
  {
    if($this->logged === true)
    {
      $network = (new UserReferral)->getNetwork($limit,$this->company_id);

      if(!$network)
      {
        return false;
      }
        
      $_network = [];

      $UserReferral = new UserReferral;
      $UserData = new UserData;

      foreach($network as $keyLevel => $level)
      {
        $_network[$keyLevel] = [];
        
        foreach($level as $key => $user_login_id)
        {
          if($userData = $this->getData($user_login_id))
          {
            $_user = [];
            $_network[$keyLevel][$key] = [];
            
            $_user = $userData;
          
            // $_user['side'] = $UserReferral->findField('user_login_id = ?',$user_login_id,"side");
            $_user['activation'] = $this->_hasProductPermission('activation',$user_login_id);
            
            if($referral_id = $UserReferral->findField("user_login_id = ?",$user_login_id,"referral_id"))
            {
              $_user['sponsor'] = [
                'sponsor_id' => $referral_id,
                'names' => $UserData->getName($referral_id),
              ];
            }

            $_network[$keyLevel][$key] = $_user;
          }
        }  
      }
      

      return $_network;
    }

    return false;
  }

  public function hasProductPermission(string $code = null)
  {
    if (!$this->getId() || !isset($code)) {
      return false;
    }

    return $this->_hasProductPermission($code,$this->company_id);
  }
  
  public function _hasProductPermission(string $code = null,int $user_login_id = null)
  {
    if (!isset($user_login_id)) {
      return false;
    }

    $product_id = (new Product)->getIdByCode($code);

    return ProductPermission::hasPermission([
      'product_id' => $product_id,
      'user_login_id' => $user_login_id
    ]);
  }
}
