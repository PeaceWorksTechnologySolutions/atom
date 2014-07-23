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
}
