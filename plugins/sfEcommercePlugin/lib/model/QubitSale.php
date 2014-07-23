<?php


/**
 * Skeleton subclass for representing a row from the 'sale' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    plugins.sfEcommercePlugin.lib.model
 */
class QubitSale extends BaseSale {

  public function __get($name)
  {
    $args = func_get_args();

    $options = array();
    if (1 < count($args))
    {
      $options = $args[1];
    }

    switch ($name)
    {
      // render_field needs this object to have a sourceCulture attribute.
      case 'sourceCulture':
        return 'en';
    }
    return call_user_func_array(array($this, 'BaseSale::__get'), $args);
  }

  public function unique_repositories() {
    $repos = array();
    foreach ($this->saleResources as $saleResource) {
      $repo = $saleResource->repository;
      if (!array_key_exists($repo->getId(), $repos)) {
        $repos[$repo->getId()] = array('id' => $repo->getId(), 'repository' => $repo);
      }
    }
    return $repos;
  }

  public function allResourcesProcessed() {
    $all_processed = true;
    foreach ($this->saleResources as $saleResource) {
      $status = $saleResource->processingStatus;
      if ($status == 'new') {
        $all_processed = false;
        break;
      }
    }
    return $all_processed;
  }

  public function hash() {
    return md5($this->createdAt . $this->firstName . $this->getId());
  }

} // QubitSale
