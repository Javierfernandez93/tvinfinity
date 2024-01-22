<?php

namespace MoneyTv;

use HCStudio\Orm;

class CatalogConference extends Orm {
  protected $tblName  = 'catalog_conference';
  
  public function __construct() {
    parent::__construct();
  }
}
