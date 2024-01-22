<?php

namespace MoneyTv;

use HCStudio\Orm;

class ConfigurationPerExercise extends Orm {
  protected $tblName  = 'configuration_per_exercise';

  public function __construct() {
    parent::__construct();
  }
  
  public static function add(array $data = null) : bool
  {
    if(isset($data) === true)
    {
      $ConfigurationPerExercise = new ConfigurationPerExercise;
      $ConfigurationPerExercise->exercise_id = $data['exercise_id'];
      $ConfigurationPerExercise->configuration = json_encode($data['configuration']);
      $ConfigurationPerExercise->buy_per_user_id = $data['buy_per_user_id'];
      $ConfigurationPerExercise->create_date = time();
      
      return $ConfigurationPerExercise->save();
    }

    return false;
  }
}