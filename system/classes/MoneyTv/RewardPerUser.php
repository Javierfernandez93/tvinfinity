<?php

namespace MoneyTv;

use HCStudio\Orm;

use MoneyTv\CatalogReward;

class RewardPerUser extends Orm {
    protected $tblName  = 'reward_per_user';
    public function __construct() {
        parent::__construct();
    }

    public static function claim(int $user_login_id = null,int $catalog_reward_id = null) : bool
    {
        $RewardPerUser = new RewardPerUser;
        
        if(!$RewardPerUser->isClaimed($user_login_id,$catalog_reward_id))
        {
            $RewardPerUser->user_login_id = $user_login_id;
            $RewardPerUser->catalog_reward_id = $catalog_reward_id;
            $RewardPerUser->create_date = time();
            
            return $RewardPerUser->save();
        }
        
        return false;
    }

    public function _getAll(int $user_login_id = null) 
    {
        $CatalogReward = new CatalogReward;
        
        if($rewards = $CatalogReward->getAll())
        {
            $Client = new Client;
            
            $activations = $Client->_getActiveCounts($user_login_id);

            return array_map(function($reward) use($activations,$user_login_id) {
                $progress = round(($activations * 100) / $reward['goal']);
                $reward['progress'] = $progress <= 100 ? $progress : 100;

                $reward['claimed'] = $this->isClaimed($user_login_id,$reward['catalog_reward_id']);

                return $reward;
            },$rewards);
        }
    }

    public function isClaimed(int $user_login_id = null,int $catalog_reward_id = null) : bool
    {
        if(isset($user_login_id,$catalog_reward_id) === true)
        {
            $sql = "SELECT
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.catalog_reward_id = '{$catalog_reward_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";
    
            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }
}