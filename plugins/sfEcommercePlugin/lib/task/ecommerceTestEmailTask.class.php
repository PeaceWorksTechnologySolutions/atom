<?php

/* For interactive testing during development */



class ecommerceTestEmail extends sfBaseTask
{
  public function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'qubit'),
    ));
    $this->namespace = 'ecommerce';
    $this->name      = 'testEmail';
  }
 
  public function execute($arguments = array(), $options = array())
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration('qubit', 'prod', false);
    sfContext::createInstance($configuration);
    $message = sfContext::getInstance()->getMailer()->compose(
          array(sfConfig::get("ecommerce_email_from_address") => sfConfig::get("ecommerce_email_from_name")),
          "jason@peaceworks.ca",
          "Test email",
          "This is the body"
        );
    sfContext::getInstance()->getMailer()->send($message);
  }
}


?>
