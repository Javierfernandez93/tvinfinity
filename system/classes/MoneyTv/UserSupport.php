<?php

namespace MoneyTv;

use HCStudio\Orm;
use HCStudio\Session;
use HCStudio\Token;
use HCStudio\Util;

use MoneyTv\CatalogUserType;
use MoneyTv\PermissionPerUserSupport;
use MoneyTv\UserType;
use MoneyTv\UserLogin;
use MoneyTv\BuyPerUser;
use MoneyTv\Log;

class UserSupport extends Orm {
  protected $tblName  = 'user_support';
  private   $session = false;
  private   $_token   = false;
  public    $_data    = [];
  public    $_parent  = [];
  public    $_parent_id = false;
  public    $_save_class = false;
  public    $_loaded  = false;
  public    $expiration_salt_date = 2;
  public    $offes_per_user = 3 ;
  public    $products_per_user = 3 ;
  public    $promo_per_user = 3 ;
  private   $field_control = 'password';
  private   $field_session = 'email';
  private   $_field_type = 'email';

  public static $DELETED = -1;
  public function __construct($save_class = false, $autoLoad = true,$redir = true) {
    parent::__construct();
    
    $this->_save_class = $save_class;
    
    $this->session = new Session($this->tblName);
    $this->_token = new Token();

    if($autoLoad)
    {
      if($this->logoutRequest()) return false ;

      if($this->checkCS() && !$this->loginRequest())
      {
        if($this->isValidPid())
          $this->login($this->_token->params[$this->field_session],$this->_token->params[$this->field_control]);

      } else if($this->loginRequest()) {
        $this->login();
      } 
    }
  }
  
  public function isSeller() : bool
  {
    if($this->_loaded === true)
    {
      return $this->catalog_user_type_id === CatalogUserType::$SELLER;
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
    return $this->session->get('pid');
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

  public function getTotalCountRegisters() {
    $first_day = mktime(0, 0, 0, 3, 1, date('Y'));
    $last_day = mktime(0, 0, 0, date('m')+1, 0, date('Y'));
    
    return $this->getCountRegisters("AND signup_date BETWEEN {$first_day} AND {$last_day}");
  }
  
  public function getActualMonthRegisters() {
    $first_day = mktime(0, 0, 0, date('m'), 1, date('Y'));
    $last_day = mktime(0, 0, 0, date('m')+1, 0, date('Y'));
    
    return $this->getCountRegisters("AND signup_date BETWEEN {$first_day} AND {$last_day}");
  }

  public function getLastMonthCountRegisters() {
    $first_day = mktime(0, 0, 0, date('m')-1, 1, date('Y'));
    $last_day = mktime(0, 0, 0, date('m'), 0, date('Y'));

    return $this->getCountRegisters("AND signup_date BETWEEN {$first_day} AND {$last_day}");
  }

  public function getCountRegisters($filter = "") {
    $sql = "SELECT 
              COUNT(user_login.user_login_id) as c
            FROM 
              user_login
            WHERE 
              user_login.status = '1'
              {$filter}
            ";
    return $this->connection()->field($sql);
  }

  public function logoutRequest(bool $logout = null) {
    $logout = ($logout) ? $logout : Util::getVarFromPGS('adminLogout');

    if($logout) return $this->logout();
  }

  public function deleteSession() {
    $this->session->destroy();
  }
  public function isAbiableToSingUp()
  {
    if(!$this->_loaded)
      if($this->getId() === 0)
        if(!$this->_data && !$this->_parent)
          return true;

    return false;
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
                {$this->tblName}.names,
                {$this->tblName}.last_login_date,
                user_settings.background,
                user_settings.personal_message,
                user_settings.gender,
                user_settings.country_id,
                user_settings.image
              FROM
                {$this->tblName}
              LEFT JOIN
                user_settings
              ON
                user_settings.user_login_id = {$this->tblName}.company_id
              WHERE
                {$this->tblName}.company_id = {$company_id}
              AND
                {$this->tblName}.status = '1'
                {$filter}";

      return $this->connection()->row($sql);
    }
    return false;
  }

  public function getCompanyIdByMail($email = false) {
    if($email)
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
      $this->_loaded = true;
    }
    return $this->_loaded;
  }

  public function login($field_session = false,$field_control = false) {
    $field_session = ($field_session) ? $field_session : Util::getVarFromPGS($this->field_session,false);
    $field_control = ($field_control) ? $field_control : sha1(Util::getVarFromPGS($this->field_control,false));

    $this->cargarDonde("{$this->field_session}=? AND {$this->field_control}=?",[$field_session,$field_control]);
    
    return $this->doLogin();
  }

  public function createPid()
  {
    $data = [
      $this->field_session => $this->{$this->field_session},
      $this->field_control => $this->{$this->field_control},
      "securitySalt" => sha1($this->last_login . $this->ip_user_address . $this->salt),
    ];
    return $this->_token->getToken($data,true,true);
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
  /* deprecated function */
  public function addGoldCoins($coins = true)
  {
    if($coins)
    {
      $this->_parent['user_coin']->gold_coin = ($this->_parent['user_coin']->gold_coin + $coins);

      if($this->_parent['user_coin']->save()) return true;
    }
    return false;
  }
  /* deprecated function */
  public function addSilverCoins($coins = true)
  {
    if($coins)
    {
      $this->_parent['user_coin']->silver_coin = ($this->_parent['user_coin']->silver_coin + $coins);

      if($this->_parent['user_coin']->save()) return true;
    }
    return false;
  }
  public function loadProfile()
  {
    $this->loadDataByClassName(__NAMESPACE__.'\UserLocation','user_location');
    $this->loadDataByClassName(__NAMESPACE__.'\UserSettings','user_settings');
    $this->loadDataByClassName(__NAMESPACE__.'\UserRegistration','user_registration');
    $this->loadDataByClassName(__NAMESPACE__.'\UserCoin','user_coin');
  }
  public function getUniqueToken($lenght = 5, $field = 'secret', $table = 'user_login', $field_as = 'total')
  {
    if($token = $this->_token->randomKey($lenght))
    {
      $sql = "SELECT count({$table}.{$field}) as {$field_as} FROM {$table} WHERE {$table}.{$field} = '{$token}'";

      if($this->connection()->field($sql)) $this->getUniqueToken();
      else return $token;
    }

    return false;
  }

  public function setPid() {
    $pid = $this->createPid();
    $this->session->set('pid',$pid);
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
      if(isset($_GET[$this->field_control]) || isset($_POST[$this->field_control])) 
      {
        return true;
      }
    }

    return false;
  }

  public function isValidPid() {
    $pid = $this->session->get('pid');  
    return ($this->_token->checkToken($pid)) ? true : false;
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
    $sql = "SELECT email FROM user_support WHERE user_support.email = '{$email}' LIMIT 1";
    return ($this->connection()->field($sql)) ? false : true;
  }

  public function isUniqueNickname($nick_name = false) {
    $sql = "SELECT nick_name FROM user_login WHERE user_login.nick_name = '{$nick_name}' LIMIT 1";
    return ($this->connection()->field($sql)) ? false : true;
  }

  public function getNames(int $user_support_id = null) 
  {
    if (isset($user_support_id) === true) {
      $sql = "SELECT 
                LOWER(CONCAT_WS(' ',{$this->tblName}.names,{$this->tblName}.last_name,{$this->tblName}.sur_name)) as names
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_support_id = '{$user_support_id}'
              ";
      return $this->connection()->field($sql); 
    }
  }

  public function getSupportData(int $user_support_id = null) 
  {
    if (isset($user_support_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.names,
                {$this->tblName}.image
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_support_id = '{$user_support_id}'
              ";

      return $this->connection()->row($sql); 
    }
  }

  public function getNickNameByUserId($user_login_id = false) {
    $sql = "SELECT user_login.nick_name FROM user_login WHERE user_login.company_id = '{$user_login_id}'";
    return $this->connection()->field($sql);
  }

  public function getCompanyIdByNickName($nick_name = false) {
    if($nick_name)
    {
      $sql = "SELECT company_id FROM user_login WHERE user_login.nick_name = '{$nick_name}' LIMIT 1";

      return $this->connection()->field($sql);
    }
    return false;
  }

  public function getUserDataByCompanyId($company_id = false) {
    return $this->_getUserDataByCompanyId($company_id);
  }

  private function _getUserDataByCompanyId($company_id = false) {
    if($company_id)
    {
        $sql = "SELECT
                    user_login.email,
                    user_login.company_id,
                    user_login.names AS names,
                    user_settings.country_id,
                    user_settings.image
                FROM
                    user_login
                LEFT JOIN
                    user_settings
                ON
                    user_settings.user_login_id = user_login.company_id
                WHERE
                    user_login.company_id = {$company_id} ";

        return $this->connection()->row($sql);
    }

    return false;
  }

  public function getUserDataByEmail($email = false) {
    return $this->_getUserDataByEmail($email);
  }

  private function _getUserDataByEmail($email = false) {
    if($email)
    {
        $sql = "SELECT
                    user_login.email,
                    user_login.company_id,
                    user_login.names AS names,
                    user_settings.country_id,
                    user_settings.image
                FROM
                    user_login
                LEFT JOIN
                    user_settings
                ON
                    user_settings.user_login_id = user_login.company_id
                WHERE
                    user_login.email = '{$email}' ";

        return $this->connection()->row($sql);
    }

    return false;
  }

  public function getUsersForSale()
  {
     $sql = "SELECT
                {$this->tblName}.nick_name as name
            FROM
                {$this->tblName}
            WHERE
                {$this->tblName}.nick_name != ''";

      return $this->connection()->rows($sql);
  }

  public function verifyUniqueNickName($nick_name){
     $sql = "SELECT
                    user_login.company_id
                FROM
                    user_login
                WHERE
                    user_login.nick_name = '{$nick_name}' ";


      $id_nick_name = $this->connection()->row($sql);

      return ($id_nick_name && $id_nick_name['company_id'] != $this->getId() ) ? true : false;
  }

  public function thereIsCoincidenceMail($email = false){
    $sql = "SELECT mail FROM user_login WHERE user_login.email LIKE '{$email}' LIMIT 1";
    return ($this->connection()->field($sql)) ? false : true;
  }

  public function getUsersQuery($name = false)
  {
    if($name) {
      $sql = "SELECT
                {$this->tblName}.company_id,
                {$this->tblName}.names,
                {$this->tblName}.email
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.names LIKE '%{$name}%'
              OR
                {$this->tblName}.email LIKE '%{$name}%'
              ";

      return $this->connection()->rows($sql);
    }

    return false;
  }

  public function getAllUsersWithNoHash()
  {
    $sql = "SELECT
              user_login.company_id,
              user_login.names,
              user_login.hash,
              user_login.email
            FROM
              user_login
            WHERE 
              user_login.hash = '@QG'
              OR
              user_login.hash = ''
            ";

    return $this->connection()->rows($sql);
  }

  public function getAll()
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.names,
              {$this->tblName}.email
            FROM
              {$this->tblName}
            WHERE 
              {$this->tblName}.active = '1'
            ";

    return $this->connection()->rows($sql);
  }
  public function getUsersAgencyForSearch() {
    $sql = "SELECT
              user_login_agency.company_id,
              user_login_agency.last_login_date,
              user_login_agency.names,
              user_login_agency.email
            FROM
              user_login_agency
            WHERE 
              user_login_agency.company_id > 0
            ORDER BY
              user_login_agency.company_id
            ASC
            ";

    return $this->connection()->rows($sql);
  }

  public function getUsersIp()
  {
      $sql = "SELECT
              user_login.company_id,
              user_login.ip_user_address
            FROM
              user_login
            LEFT JOIN 
              user_location 
            ON
              user_location.company_id = user_login.company_id
            WHERE 
              user_login.company_id > 0
            AND
              user_login.status = '1'
            AND
              user_login.ip_user_address != ''
            AND 
              user_location.locale = '' 
            ";

      return $this->connection()->rows($sql);
  }

  public function getUsersForSearch($filter = "")
  {
    $sql = "SELECT
              user_login.company_id,
              user_setting.personal_message,
              user_setting.gender,
              user_setting.age,
              user_setting.image,
              user_setting.phone,
              user_setting.country,
              user_login.last_login_date,
              user_data.names,
              user_login.email,
              user_login.signup_date
            FROM
              user_login
            LEFT JOIN
              user_setting
            ON
              user_setting.user_login_id = user_login.user_login_id
            LEFT JOIN
              user_data
            ON
              user_data.user_login_id = user_login.user_login_id
            WHERE 
              user_login.company_id > 0
              {$filter}
            ORDER BY
              user_login.company_id
            ASC
            LIMIT 10
            ";

    return $this->connection()->rows($sql);
  }

  public function getUsersCount()
  {
    $sql = "SELECT
              COUNT(user_login.company_id) as c
            FROM
              user_login
            ";

    return $this->connection()->field($sql);
  }

  public function getUnactiveUsers() {
    $time = strtotime("- 2 months");
    $sql = "SELECT
              user_login.company_id,
              user_login.last_login_date,
              user_login.names
            FROM
              user_login
            WHERE 
              user_login.last_login_date < '{$time}'
            ORDER BY
              user_login.company_id
            ASC
            ";

    return $this->connection()->rows($sql);
  }

  public function getInfo($user_support_id = null)  
  {
    if(isset($user_support_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.phone,
                {$this->tblName}.names,
                {$this->tblName}.image
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.user_support_id = '{$user_support_id}'
                ";

      return $this->connection()->row($sql);
    }
    return false;
  }

  public function getUserType() 
  {
    if($this->_loaded === true)
    {
      $CatalogUserType = new CatalogUserType;
      
      return ucfirst($CatalogUserType->getUserType($this->catalog_user_type_id));
    }

    return false;
  }

  public function getImageForProfile() {
    return isset($this->image) === true ? $this->image : '../../src/img/no-image.png';
  }

  public function getShortName() {
    return $this->names;
  }

  public function deleteClient($user_login_id = null)
  {
    if(isset($user_login_id) === true)
    {
      $UserLogin = new UserLogin;

      if($UserLogin->cargarDonde("user_login_id = ?",$user_login_id))
      {
        $UserLogin->status = UserLogin::$DELETED;

        return $UserLogin->save();
      }
    }

    return false;
  }

  public function deleteSeller($user_support_id = null)
  {
    if(isset($user_support_id) === true)
    {
      $UserSupport = new UserSupport(false,false);

      if($UserSupport->cargarDonde("user_support_id = ?",$user_support_id))
      {
        $UserSupport->status = UserSupport::$DELETED;

        return $UserSupport->save();
      }
    }

    return false;
  }

  public function getAllClients($filter = "")
  {
    $sql = "SELECT
              user_login.user_login_id,
              user_login.email,
              user_login.verified,
              user_account.image,
              user_contact.phone,
              user_contact.cellular,
              LOWER(CONCAT_WS(' ',user_data.names,user_data.last_name,user_data.sur_name)) as names,
              client_per_seller.user_support_id
            FROM
              user_login
            LEFT JOIN 
              user_data
            ON 
              user_data.user_login_id = user_login.user_login_id
            LEFT JOIN 
              user_contact
            ON 
              user_contact.user_login_id = user_login.user_login_id
            LEFT JOIN 
              user_account
            ON 
              user_account.user_login_id = user_login.user_login_id
            LEFT JOIN 
              user_type
            ON 
              user_type.user_login_id = user_login.user_login_id
            LEFT JOIN 
              client_per_seller
            ON 
              client_per_seller.user_login_id = user_login.user_login_id
            WHERE
              user_login.status = '1'
            AND 
              user_type.catalog_user_type_id = '1'
              {$filter}
              ";
              
    return $this->connection()->rows($sql);
  }

  public function getAllSellers()
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.email,
              {$this->tblName}.phone,
              {$this->tblName}.cellular,
              {$this->tblName}.image,
              LOWER(CONCAT_WS(' ',{$this->tblName}.names,{$this->tblName}.last_name,{$this->tblName}.sur_name)) as names
            FROM
              {$this->tblName}
            WHERE
              {$this->tblName}.status = '1' 
            AND 
              {$this->tblName}.catalog_user_type_id = '".CatalogUserType::$SELLER."'
            ORDER BY 
              {$this->tblName}.create_date 
            DESC
              ";

    return $this->connection()->rows($sql);
  }

  public function getBeneficiaries($user_login_id = null)
  {
    if(isset($user_login_id) === true)
    {
      $UserType = new UserType;

      if($users = $UserType->getByType($user_login_id,CatalogUserType::$BENEFICIARY))
      {
        foreach ($users as $key => $user) {
          $_users[$key] = $this->getClient($user,CatalogUserType::$BENEFICIARY);
        }

        return $_users;
      }
    }

    return false;
  }

  public function getAvals($user_login_id = null)
  {
    if(isset($user_login_id) === true)
    {
      $UserType = new UserType;

      if($users = $UserType->getByType($user_login_id,CatalogUserType::$AVAL))
      {
        foreach ($users as $key => $user) {
          $_users[$key] = $this->getClient($user,CatalogUserType::$AVAL);
        }

        return $_users;
      }
    }

    return false;
  }

  public function getClient($user_login_id = null,$catalog_user_type_id = 1)
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                user_login.user_login_id,
                user_login.email,
                user_contact.phone,
                user_contact.cellular,
                user_data.names,
                user_data.gender,
                user_data.birthday,
                user_data.last_name,
                user_data.sur_name,
                LOWER(CONCAT_WS(' ',user_data.names,user_data.last_name,user_data.sur_name)) as name
              FROM
                user_login
              LEFT JOIN 
                user_data
              ON 
                user_data.user_login_id = user_login.user_login_id
              LEFT JOIN 
                user_contact
              ON 
                user_contact.user_login_id = user_login.user_login_id
              LEFT JOIN 
                user_type
              ON 
                user_type.user_login_id = user_login.user_login_id
              WHERE
                user_login.user_login_id = '{$user_login_id}'
              AND 
                user_login.status = '1'
              AND 
                user_type.catalog_user_type_id = '{$catalog_user_type_id}'
                ";
                
      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getSeller($user_support_id = null)
  {
    if(isset($user_support_id) === true)
    {
      return $this->getUserSupport($user_support_id);
    }

    return false;
  }

  public function getUserSupport($user_support_id = null)
  {
    if(isset($user_support_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.names,
                {$this->tblName}.last_name,
                {$this->tblName}.sur_name,
                {$this->tblName}.email,
                {$this->tblName}.gender,
                {$this->tblName}.phone,
                {$this->tblName}.cellular
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.user_support_id = '{$user_support_id}'
              AND 
                {$this->tblName}.status = '1'
                ";

      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getAdmin($user_support_id = null)
  {
    if(isset($user_support_id) === true)
    {
      return $this->getUserSupport($user_support_id);
    }

    return false;
  }

  public function getAllSupports($user_support_id = null)
  {
    $filter = isset($user_support_id) ? "AND {$this->tblName}.user_support_id != '{$user_support_id}'" : '';

    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              LOWER(CONCAT_WS(' ',{$this->tblName}.names,{$this->tblName}.last_name,{$this->tblName}.sur_name)) as names,
              {$this->tblName}.email,
              {$this->tblName}.image,
              {$this->tblName}.cellular,
              {$this->tblName}.phone,
              {$this->tblName}.create_date
            FROM
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            AND 
              {$this->tblName}.catalog_user_type_id = '".CatalogUserType::$ADMIN."'
              {$filter}
              ";

    return $this->connection()->rows($sql);
  }

  public function getAllSupportsFilter($name = null,$filter = "")
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              LOWER(CONCAT_WS(' ',{$this->tblName}.names,{$this->tblName}.last_name,{$this->tblName}.sur_name)) as names,
              {$this->tblName}.email,
              {$this->tblName}.image,
              {$this->tblName}.cellular,
              {$this->tblName}.phone,
              {$this->tblName}.create_date
            FROM
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            AND 
              {$this->tblName}.catalog_user_type_id = '".CatalogUserType::$SELLER."'
            AND 
              {$this->tblName}.names LIKE '%{$name}%'
              {$filter}
              ";

    return $this->connection()->rows($sql);
  }

  public function countAdminUsers($catalog_user_type_id = null)
  {
    if(isset($catalog_user_type_id) === true)
    {
      $sql = "SELECT
                COUNT({$this->tblName}.{$this->tblName}_id) as c
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.status = '1'
              AND 
                {$this->tblName}.catalog_user_type_id = '{$catalog_user_type_id}'
                ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function countUsers($catalog_user_type_id = null)
  {
    if(isset($catalog_user_type_id) === true)
    {
      $sql = "SELECT
                COUNT(user_type.user_type_id) as c
              FROM
                user_type
              WHERE 
                user_type.status = '1'
              AND 
                user_type.catalog_user_type_id = '{$catalog_user_type_id}'
                ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getUsers($filter = '')
  {
    $sql = "SELECT
              user_login.user_login_id,
              user_login.catalog_campaing_id,
              user_login.signup_date,
              user_login.company_id,
              user_login.email,
              user_account.image,
              user_data.names,
              user_address.country_id,
              user_contact.phone
            FROM
              user_login
            LEFT JOIN 
              user_data
            ON 
              user_data.user_login_id = user_login.user_login_id
            LEFT JOIN 
              user_account
            ON 
              user_account.user_login_id = user_login.user_login_id
            LEFT JOIN 
              user_contact
            ON 
              user_contact.user_login_id = user_login.user_login_id
            LEFT JOIN 
              user_address
            ON 
              user_address.user_login_id = user_login.user_login_id
            WHERE 
              user_login.status = '1'
              {$filter}
            GROUP BY user_login.user_login_id
            ORDER BY 
              user_login.signup_date
            DESC
              ";

    return $this->connection()->rows($sql);
  }
  
  public function getUserReferralId(int $user_login_id = null)
  {
    return (new UserReferral)->getUserReferralId($user_login_id);
  }

  public function getUserCredentials(int $user_login_id = null)
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                user_login.email,
                user_login.password
              FROM
                user_login
              WHERE 
                user_login.status = '1'
              AND 
                user_login.user_login_id = '{$user_login_id}'
                ";

      return $this->connection()->row($sql);
    }
  }

  public function getUser(int $user_login_id = null)
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                user_login.user_login_id,
                user_login.company_id,
                user_login.signup_date,
                user_login.email,
                user_account.image,
                user_data.names,
                user_contact.phone,
                user_address.country_id,
                user_plan.ammount,
                user_plan.sponsor_profit,
                user_plan.additional_profit,
                user_plan.catalog_plan_id
              FROM
                user_login
              LEFT JOIN 
                user_data
              ON 
                user_data.user_login_id = user_login.user_login_id
              LEFT JOIN 
                user_plan
              ON 
                user_plan.user_login_id = user_login.user_login_id
              LEFT JOIN 
                user_account
              ON 
                user_account.user_login_id = user_login.user_login_id
              LEFT JOIN 
                user_address
              ON 
                user_address.user_login_id = user_login.user_login_id
              LEFT JOIN 
                user_contact
              ON 
                user_contact.user_login_id = user_login.user_login_id
              WHERE 
                user_login.status = '1'
              AND 
                user_login.user_login_id = '{$user_login_id}'
              ORDER BY 
                user_login.signup_date
              DESC
                ";

      return $this->connection()->row($sql);
    }
  }
  public function getUserEmail(int $user_login_id = null)
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                user_login.email
              FROM
                user_login
              WHERE 
                user_login.user_login_id = '{$user_login_id}'
                ";

      return $this->connection()->field($sql);
    }
  }

  public function getAdministrators($filter = '')
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.names,
              {$this->tblName}.email,
              {$this->tblName}.create_date
            FROM
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            ORDER BY 
              {$this->tblName}.create_date
            DESC
              ";

    return $this->connection()->rows($sql);
  }
  
  public function getCountUsers()
  {
    $sql = "SELECT
              COUNT(user_login.user_login_id) as c
            FROM
              user_login
            WHERE 
              user_login.status = '1'
              ";

    return $this->connection()->field($sql);
  }
  
  public function getBuysList(string $filter = null)
  {
    if($this->getId())
    {
      return (new BuyPerUser)->getList($filter);
    }
  }
  
  public function addLog(array $data = null,int $log_type = null)
  {
    if($this->getId())
    {
      return Log::addNewRecord($this->getId(),$data,$log_type);
    }
  }
}