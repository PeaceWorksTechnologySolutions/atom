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
      $body .= "    " . strtoupper($repo['repository']->identifier) . "\n";
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
        $reponame = strtoupper($repo->identifier);
        $body = "This email is in regard to your order from $site_title,";
        $body .= "which includes photos from $reponame.\n\n";

        $user_settings_query = $repo->userEcommerceSettingss;
        foreach ($user_settings_query as $user_settings) {
          $body .= $user_settings->vacationMessage;
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

  /* amount is a numeric amount (as a string)
     ratios is an array of percentages (as strings)
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
      print $repoid . ": " . $sale->totalAmount . " / " . $share . "\n";
      $ratios[] = bcdiv($share, $sale->totalAmount, 10);
      $shares[] = $share;
    }
    $fee_shares = sfEcommercePlugin::allocate_money($sale->transactionFee, $ratios);

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

      $i += 1;
    }
  }

  public static function record_refund_transactions($sale, $refund_transaction_id, $refund_amount, $fee_amount)
  {
    $repos = array();
    foreach ($sale->saleResources as $saleResource) {
      if ($saleResource['refundTransactionId'] == $refund_transaction_id) {
        $repos[$saleResource->repository->getId()] = $saleResource->repository;
        $repo = $saleResource->repository;
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
  }

}
