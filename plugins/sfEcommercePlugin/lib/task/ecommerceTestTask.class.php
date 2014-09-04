<?php

/* For interactive testing during development */



class ecommerceTest extends sfBaseTask
{
  public function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'qubit'),
    ));
    $this->namespace = 'ecommerce';
    $this->name      = 'test';
  }
 
  public function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $criteria = new Criteria;
    $criteria->add(QubitObject::ID, 481);
    $sale = QubitObject::get($criteria)->__get(0);
    //sfEcommercePlugin::set_applicable_taxes($sale);
    //$result = sfEcommercePlugin::calculate_taxes($sale);
    //sfEcommercePlugin::record_purchase_transactions($sale);

    $resources = array();
    $resources[] = 378;
    $result = sfEcommercePlugin::calculate_taxes_on_resources($sale, $resources);
    print print_r($result, true);


    //$result = sfEcommercePlugin::country_subdivisions('DE');
    //print print_r($result, true) . "\n";
  }
}


?>
