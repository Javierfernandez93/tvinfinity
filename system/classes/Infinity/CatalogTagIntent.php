<?php

namespace Infinity;

use HCStudio\Orm;

class CatalogTagIntent extends Orm {
    protected $tblName  = 'catalog_tag_intent';
    public function __construct() {
        parent::__construct();
    }
}