<?php

namespace Infinity;

use HCStudio\Orm;

class CatalogValidationMethod extends Orm {
  protected $tblName  = 'catalog_validation_method';

  /* constants */
  const ADMINISTRATOR = 1;
  const COINPAYMENTS_IPN = 2;
  const EWALLET = 3;
  const PAYPAL_CDN = 4;
  const INTERNAL_USER = 5;

  public function __construct() {
    parent::__construct();
  }
}