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

  public static function subtotal($resources)
  {
    $subtotal = '0';
    foreach ($resources as $resource) {
      $subtotal = bcadd($subtotal, sfEcommercePlugin::resource_price($resource), 2);
    }
    return $subtotal;
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
    $link = str_replace('https', 'http', $link);
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

  public static function notify_customer($sale)
  {
    $on_vacation = array();
    $repos = sfEcommercePlugin::sale_resources_by_repository($sale);
    foreach ($repos as $repoid => $repo) {
      // get users for this respository
      $recipients = array();
      $user_settings_query = $repo['repository']->userEcommerceSettingss;

      $allusers_on_vacation = true;
      foreach ($user_settings_query as $user_settings) {
        if (!$user_settings->vacationEnabled) {
          $allusers_on_vacation = false;
        }
      }
      if ($allusers_on_vacation) {
        $on_vacation[] = $repoid;
      }
    }

    $site_title = sfConfig::get('app_siteTitle');
    $body = "Thank you for your order from $site_title.  Your order includes photos from the following archives:\n";
    foreach ($repos as $repoid => $repo) {
      $body .= "    " . strtoupper($repo['repository']->authorizedFormOfName) . "\n";
    }
    $body .= "\nWhen each archives processes your order, you will receive an email allowing you to download your photos.";

    $message = sfContext::getInstance()->getMailer()->compose(
      array(sfConfig::get("ecommerce_email_from_address") => sfConfig::get("ecommerce_email_from_name")),
      $sale->email,
      "Your order from $site_title",
      $body
    );
    sfContext::getInstance()->getMailer()->send($message);

    // ensure 2nd email arrives after the 1st one
    sleep(2);

    if (count($on_vacation) > 0) {
      foreach($on_vacation as $repoid) {
        $repo = $repos[$repoid]['repository'];
        $reponame = strtoupper($repo->authorizedFormOfName);
        $body = "This email is in regard to your order from $site_title, ";
        $body .= "which includes photos from $reponame.\n\n";

        $user_settings_query = $repo->userEcommerceSettingss;
        foreach ($user_settings_query as $user_settings) {
          if ($user_settings->vacationMessage) {
            $body .= $user_settings->vacationMessage;
            break;
          }
        }

        $message = sfContext::getInstance()->getMailer()->compose(
          array(sfConfig::get("ecommerce_email_from_address") => sfConfig::get("ecommerce_email_from_name")),
          $sale->email,
          "Note from $reponame re: your order",
          $body
        );
        sfContext::getInstance()->getMailer()->send($message);
      }
    }
  }

  /* amount is a numeric amount (as a string) to be divided into shares
     ratios is an array of share percentages (as strings)
     Return: an array of dollar amounts which are guaranteed to total to the original amount.
     Credit: http://stackoverflow.com/questions/1679292/proof-that-fowlers-money-allocation-algorithm-is-correct
  */
  public static function allocate_money($amount, $ratios)
  {
    $total = '0';
    foreach ($ratios as $ratio) {
      $total = bcadd($total, $ratio, 10);
    }

    $remainder = $amount;
    $result = array();
    foreach ($ratios as $ratio) {
      $share = bcdiv(bcmul($amount, $ratio, 10), $total, 2);
      $result[] = $share;
      $remainder = bcsub($remainder, $share, 2);
    }

    for ($i = 0; $i < bcmul($remainder, 100); $i++) {
        $result[$i] = bcadd($result[$i], '0.01', 2);
    }
    return $result;
  }

  /* 
    Credit: http://stackoverflow.com/questions/1642614/how-to-ceil-floor-and-round-bcmath-numbers
  */
  /*
  assert(bcround('3', 0) == '3');  // true
  assert(bcround('3.4', 0) == '3'); // true
  assert(bcround('3.5', 0) == '4'); // true
  assert(bcround('3.6', 0) == '4');  // true
  assert(bcround('1.95583', 1) == '2.0'); // true
  assert(bcround('1.94999', 1) == '1.9'); // true
  assert(bcround('1.95583', 2) == '1.96'); // true
  assert(bcround('1.95499', 2) == '1.95'); // true
  assert(bcround('5.045', 2) == '5.05'); // true
  assert(bcround('5.055', 2) == '5.06'); // true
  assert(bcround('9.999', 2) == '10.00'); // true
  */

  public static function bcround($number, $precision = 0)
  {
      if (strpos($number, '.') !== false) {
          if ($number[0] != '-') return bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
          return bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
      }
      return $number;
  }


  public static function record_purchase_transactions($sale) 
  {
    $repos = sfEcommercePlugin::sale_resources_by_repository($sale);
    $ratios = array();
    $shares = array();
    foreach ($repos as $repoid => $repo) {
      $share = '0';
      foreach ($repo['saleResources'] as $saleResource) {
        $share = bcadd($share, $saleResource->price, 2);
      }
      $ratios[] = bcdiv($share, $sale->totalAmount, 10);
      $shares[] = $share;
    }
    $fee_shares = sfEcommercePlugin::allocate_money($sale->transactionFee, $ratios);
    $taxes = sfEcommercePlugin::calculate_taxes($sale);


    $i = 0;
    foreach ($repos as $repoid => $repo) {
      $transaction = new QubitEcommerceTransaction();
      $transaction->setSale($sale);
      $transaction->setRepository($repo['repository']);
      $transaction->setAmount($shares[$i]);
      $transaction->setType('sale');
      $transaction->save();

      $transaction = new QubitEcommerceTransaction();
      $transaction->setSale($sale);
      $transaction->setRepository($repo['repository']);
      $transaction->setAmount('-' . $fee_shares[$i]);
      $transaction->setType('sale fees');
      $transaction->save();

      foreach ($taxes[$repo['repository']->identifier] as $taxname => $taxinfo) {
        $transaction = new QubitEcommerceTransaction();
        $transaction->setSale($sale);
        $transaction->setRepository($repo['repository']);
        $transaction->setAmount($taxinfo['taxAmount']);
        $transaction->setType('tax ' . $taxname);
        $transaction->save();
      }

      $i += 1;
    }
  }

  public static function record_refund_transactions($sale, $refund_transaction_id, $refund_amount, $fee_amount)
  {
    $repos = array();
    $refund_resources = array();
    foreach ($sale->saleResources as $saleResource) {
      if ($saleResource['refundTransactionId'] == $refund_transaction_id) {
        $repos[$saleResource->repository->getId()] = $saleResource->repository;
        $repo = $saleResource->repository;
        $refund_resources[] = $saleResource->resource->getId();
      }
    }

    if (count($repos) == 0) {
      return;
    } elseif (count($repos) > 1) {
      sfContext::getInstance()->getLogger()->err("got refund transaction $refund_transaction_id which affects multiple repositories!");
      return;
    }

    $transaction = new QubitEcommerceTransaction();
    $transaction->setSale($sale);
    $transaction->setRepository($repo);
    $transaction->setAmount($refund_amount);
    $transaction->setType('refund');
    $transaction->save();

    if ($fee_amount[0] == '-') {
      $fee_amount = substr($fee_amount, 1);
    }
    $transaction = new QubitEcommerceTransaction();
    $transaction->setSale($sale);
    $transaction->setRepository($repo);
    $transaction->setAmount($fee_amount);
    $transaction->setType('fee refund');
    $transaction->save();

    $taxes = sfEcommercePlugin::calculate_taxes_on_resources($sale, $refund_resources);
    foreach ($taxes as $taxname => $taxamount) {
      $transaction = new QubitEcommerceTransaction();
      $transaction->setSale($sale);
      $transaction->setRepository($repo);
      $transaction->setAmount('-' . $taxamount);
      $transaction->setType('tax refund ' . $taxname);
      $transaction->save();
    }
  }

  public static function set_applicable_taxes($sale)
  {
    // determine which taxes (and rates) apply to each resource in the sale.
    // store this information in each SaleResource records.

    $repos = sfEcommercePlugin::sale_resources_by_repository($sale);
    foreach ($repos as $repoid => $repo) {
      foreach ($repo['saleResources'] as $saleResource) {
        sfEcommercePlugin::set_applicable_taxes_for_resource($sale, $saleResource, $repo['repository']);
      }
    }

    //sfEcommerceTaxes::calculate_taxes($this->resource);
  }

  public static function set_applicable_taxes_for_resource($sale, $saleResource, $repo)
  {
    $taxes = sfEcommerceTaxes::determine_taxes($sale, $saleResource, $repo, $taxname);
    $i = 1;
    foreach ($taxes as $taxname => $rate) {
      if (isset($rate) && $rate != '0') {
        $saleResource["tax" . $i . "Name"] = $taxname;
        $saleResource["tax" . $i . "Rate"] = $rate;
        $saleResource->save();
        $i += 1;
      }
    }
  }

  // Calculate total tax amount on a subset of resources (passed an an array of resource IDs) of a sale.
  public static function calculate_taxes_on_resources($sale, $resource_ids)
  {
    $taxes = array();

    foreach ($sale->saleResources as $saleResource) {
      if (in_array($saleResource->resource->getId(), $resource_ids)) {
        foreach (array('tax1', 'tax2') as $tax) {
          if (!empty($saleResource[$tax . 'Name'])) {
            $taxname = $saleResource[$tax . 'Name'];
            $rate = $saleResource[$tax . 'Rate'];
            if (!isset($taxes[$taxname])) {
              $taxes[$taxname] = '0';
            }
            $rate = bcdiv($rate, 100, 4);
            $amount = bcmul($rate, $saleResource['price'], 4);
            $taxes[$taxname] = bcadd($taxes[$taxname], $amount, 4);
          }
        }
      }
    }
    // round to 2 decimal places
    foreach ($taxes as $taxname => $amount) {
      $taxes[$taxname] = sfEcommercePlugin::bcround($amount, 2);
    }
    return $taxes;
  }

  public static function calculate_taxes($sale)
  {
    // based on the taxes and rates set in the SaleResource records,
    // calculate the taxes for each repository.
    $repos = sfEcommercePlugin::sale_resources_by_repository($sale);
    $result = array();
    foreach ($repos as $repoid => $repo) {
      $taxes = array();

      // compile an array of taxes, their rates, and dollar amounts on which they apply.
      foreach ($repo['saleResources'] as $saleResource) {
        foreach (array('tax1', 'tax2') as $tax) {
          if (!empty($saleResource[$tax . 'Name'])) {
            $taxname = $saleResource[$tax . 'Name'];
            $rate = $saleResource[$tax . 'Rate'];
            if (isset($taxes[$taxname])) {
              assert($taxes[$taxname]['rate'] == $rate);
            } else {
              $taxes[$taxname]['rate'] = $rate;
              $taxes[$taxname]['onAmount'] = '0';
            }
            $taxes[$taxname]['onAmount'] = bcadd($taxes[$taxname]['onAmount'], $saleResource->price, 2);
          }
        }
      }

      // now calculate the total tax amounts
      foreach ($taxes as $taxname => $unused) {
        $amount = bcmul(bcdiv($taxes[$taxname]['rate'], 100, 4), $taxes[$taxname]['onAmount'], 4);
        $taxes[$taxname]['taxAmount'] = sfEcommercePlugin::bcround($amount, 2);
      }
      $result[$repo['repository']->identifier] = $taxes;
    }
    return $result;
  }

  public static function country_subdivisions($country_id)
  {
    // This file comes from http://www.geonames.org/
    // Direct URL: http://download.geonames.org/export/dump/admin1CodesASCII.txt
    $handle = fopen(dirname(__FILE__) . "/admin1CodesASCII.txt", "r");
    $subdivisions = array();
    if ($handle) {
      while (($line = fgets($handle)) !== false) {
        if (strpos($line, $country_id . ".") === 0) {
          $parts = explode("\t", $line);
          $subdivisions[] = $parts[1];
        }
      }
    }
    sort($subdivisions);
    return $subdivisions;
  }

  public static function user_get_ecommerce_settings($user) {
    $criteria = new Criteria;
    $criteria->add(QubitUserEcommerceSettings::USER_ID, $user->getId());
    return QubitUserEcommerceSettings::getOne($criteria);
  }
}
