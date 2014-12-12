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

    echo QubitInformationObject::ROOT_ID . "\n";
    return;

    $criteria = new Criteria;
    $criteria->add(QubitObject::ID, 629);
    $sale = QubitObject::get($criteria)->__get(0);
    //sfEcommercePlugin::set_applicable_taxes($sale);
    //$result = sfEcommercePlugin::calculate_taxes($sale);
    //sfEcommercePlugin::record_purchase_transactions($sale);

    //$resources = array();
    //$resources[] = 378;
    //$result = sfEcommercePlugin::calculate_taxes_on_resources($sale, $resources);
    //print print_r($result, true);

    $sale_repos = sfEcommercePlugin::sale_resources_by_repository($sale);
    $repo = $sale_repos[318]['repository'];
    
    echo $repo->authorizedFormOfName . "\n";
    $contact = $repo->getPrimaryContact();
    echo $contact->contactPerson . "\n";
    echo $contact->telephone . "\n";
    echo $contact->email . "\n";
    echo $contact->website . "\n";

    //$result = sfEcommercePlugin::country_subdivisions('DE');
    //print print_r($result, true) . "\n";
  }
}


?>
