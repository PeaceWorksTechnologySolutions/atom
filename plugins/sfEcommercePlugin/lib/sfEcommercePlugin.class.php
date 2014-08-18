<?php

/*
 */

class sfEcommercePlugin 
{
  public static function resource_price($resource)
  {
    $repo = $resource->getRepository(array('inherit' => true));
    if (!isset($repo)) {
        return NULL;
    }
    $price = sfConfig::get("ecommerce_" . $repo->slug . "_price");
    return $price;
  }

  public static function fetch_cart_resources($cart_contents)
  {
    $resources = array();
    foreach ($cart_contents as $slug ) 
    {
      $resource = sfEcommercePlugin::fetch_resource($slug);
      $resources[] = $resource;
    }
    return $resources;
  }

  public static function fetch_resource($slug) 
  {
    $criteria = new Criteria;
    $criteria->add(QubitSlug::SLUG, $slug);
    $criteria->addJoin(QubitSlug::OBJECT_ID, QubitObject::ID);

    return QubitObject::get($criteria)->__get(0);
  }

  public static function sale_resources_by_repository($sale) 
  {
    $repos = $sale->unique_repositories();
    
    foreach ($repos as $repoid => $repo) {
      $repos[$repoid]['resources'] = array();
      $repos[$repoid]['saleResources'] = array();
      foreach ($sale->saleResources as $saleResource) {
        if ($saleResource->repository->getId() == $repoid) {
          $repos[$repoid]['resources'][] = $saleResource->resource;
          $repos[$repoid]['saleResources'][] = $saleResource;
        }
      }
    }
    return $repos;
  }


  public static function notify_repositories($sale) 
  {
    $site_title = sfConfig::get('app_siteTitle');
    $purchaser = $sale->firstName . " " . $sale->lastName;
    $link = sfContext::getInstance()->getController()->genUrl(array('module' => 'sfEcommercePlugin', 'action' => 'viewOrder', 'id' => $sale->getId()), true);
    $repos = sfEcommercePlugin::sale_resources_by_repository($sale);
    foreach ($repos as $repoid => $repo) {
      // get users for this respository
      $recipients = array();
      $user_settings_query = $repo['repository']->userEcommerceSettingss;
      foreach ($user_settings_query as $user_settings) {
        $recipients[] = $user_settings->user->email;
      }

      $photo_list = '';
      foreach ($repo['resources'] as $resource) {
        $photo_list .= $resource->referenceCode . ' (Title: ' . $resource->title . ')' . "\n";
      }

      // send email with the list of resources
      $message = sfContext::getInstance()->getMailer()->compose(

      array(sfConfig::get("ecommerce_email_from_address") => sfConfig::get("ecommerce_email_from_name")),
      $recipients,
      "Order from $purchaser received at $site_title",
      <<<EOF
An order has been received from $purchaser for the following photos:

$photo_list

View the order at: 
  $link
EOF
      );
      sfContext::getInstance()->getMailer()->send($message);
    }
    return $repos;
  }
}
