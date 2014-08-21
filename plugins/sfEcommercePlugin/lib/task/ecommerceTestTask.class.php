<?php

/* For interactive testing during development */

class ecommerceTest extends sfBaseTask
{
  public function configure()
  {
    $this->namespace = 'ecommerce';
    $this->name      = 'test';
  }
 
  public function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $criteria = new Criteria;
    $criteria->add(QubitObject::ID, 473);
    $sale = QubitObject::get($criteria)->__get(0);
    sfEcommercePlugin::record_purchase_transactions($sale);
  }
}


?>
