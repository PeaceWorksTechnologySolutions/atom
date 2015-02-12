<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

class sfEcommercePluginViewOrderAction extends sfAction
{
  public function execute($request)
  {

    $settings = sfEcommercePlugin::user_get_ecommerce_settings($this->getUser()->user);
    if (!$settings || !($settings->repository)) {
        $this->redirect('admin/secure');
    }

    $criteria = new Criteria;
    $criteria->add(QubitObject::ID, $request->getParameter('id'));
    $this->resource = QubitObject::get($criteria)->__get(0);

    $repos = sfEcommercePlugin::sale_resources_by_repository($this->resource);
    $user_repo = $settings->repository->getId();
    $this->resources = $repos[$user_repo]['resources'];
    $this->saleResources = $repos[$user_repo]['saleResources'];
    $current_repo = $repos[$user_repo]['repository'];

    // check whether all resources have been processed (for this repository)
    $this->allResourcesProcessed = true;
    foreach ($this->saleResources as $saleResource) {
      if ($saleResource->processingStatus == 'new') {
        $this->allResourcesProcessed = false;
        break;
      }
    }

    if ($request->isMethod('post'))
    {
      $fields = $request->getPostParameters();
      sfContext::getInstance()->getLogger()->warning(print_r($fields, true));

      // process order
      if (array_key_exists('process', $fields)) {
        sfContext::getInstance()->getLogger()->warning('processing!');

        $rejected_resources = array();
        $rejected_resource_ids = array();
        $accepted_resources = array();
        foreach ($this->resources as $index => $resource) {
          if ($fields['confirm_' . $resource->getId()] == 'accept') {
            $accepted_resources[] = $resource->getId();
            $this->saleResources[$index]['processingStatus'] = 'accepted';
            $this->saleResources[$index]->save();
          } elseif ($fields['confirm_' . $resource->getId()] == 'reject') {
            $rejected_resource_ids[] = $resource->getId();
            $rejected_resources[] = $resource;
          }
        }

        // refund any rejected resources
        if (count($rejected_resource_ids) > 0) {
          $note = "Refund for " . count($rejected_resource_ids) . " photo(s) from " . $current_repo->authorizedFormOfName;
          $this->refundResources($rejected_resource_ids, $note);
        }
  
        $this->notifyCustomer($current_repo, $rejected_resources, $accepted_resources, $fields['note']);
        $this->updateSaleStatus();

        $this->redirect(array('module' => 'sfEcommercePlugin', 'action' => 'browseOrders'));

      // anonymize order
      } else if (array_key_exists('anonymize', $fields)) {
        $this->resource['firstName'] = 'anonymous';
        $this->resource['lastName'] = '';
        $this->resource['address1'] = '';
        $this->resource['address2'] = '';
        $this->resource['city'] = '';
        $this->resource['province'] = '';
        $this->resource['postalCode'] = '';
        $this->resource['email'] = '';
        $this->resource['phone'] = '';
        $this->resource->save();
      }
    }
  }

  public function updateSaleStatus()
  {
    $this->resource['lastProcessedAt'] = date('Y-m-d H:i:s');
    if ($this->resource->allResourcesProcessed()) {
      $this->resource['processingStatus'] = 'processed';
    }
    $this->resource->save();
  }

  public function notifyCustomer($repository, $rejected_resources, $accepted_resources, $note)  
  {
    $site_title = sfConfig::get('app_siteTitle');
    $reponame = strtoupper($repository->authorizedFormOfName);
    $body = "$reponame has processed your order.\n";
      
    if (!$this->resource->allResourcesProcessed()) {
      $body .= "\nIMPORTANT: some photos in your order have not yet been processed.  This email only pertains to the photos ordered from $reponame.  You will receive further email when the remaining photos are processed.\n\n";
    }

    if (count($rejected_resources) > 0) {
      $body .= "The Archives could not fulfill your request for the following photos:\n";
      foreach($rejected_resources as $item) {
        $body .= "    " . $item->referenceCode . ' (Title: ' . $item->title . ')' . "\n";
      }
      $body .= "\nYou have been refunded for these items.\n";
    }

    if (!empty($note)) {
      $body .= "\nNote from the Archives:\n$note\n";
    }

    if (count($accepted_resources) > 0) {
      if (count($rejected_resources) > 0) {
        $body .= "\nYou may download the other photos in your order here:\n";
      } else {
        $body .= "\nYou may download the photos here:\n";
      }
      $body .= $this->getController()->genUrl(array('module' => 'sfEcommercePlugin', 'action' => 'download', 'id' => $this->resource->getId(), 'hash' => $this->resource->hash()), true);

      if ($this->resource->allResourcesProcessed()) {
        $body .="\nPlease download and save a copy of your photos, since this link will remain valid for only 10 days.\n";
      } else {
        $body .="\nPlease download and save a copy of your photos, since this link will remain valid for only 10 days after your order is fully processed.\n";
      }
    }

    $contact = $repository->getPrimaryContact();
    $body .= "\n\n";
    $body .= $repository->authorizedFormOfName . "\n";
    if (!empty($contact->telephone)) {
        $body .= 'Phone: ' . $contact->telephone . "\n";
    }
    if (!empty($contact->email)) {
        $body .= 'Email: ' . $contact->email . "\n";
    }
    if (!empty($contact->website)) {
        $body .= 'Web: ' . $contact->website . "\n";
    }

    $message = sfContext::getInstance()->getMailer()->compose(
      array(sfConfig::get("ecommerce_email_from_address") => sfConfig::get("ecommerce_email_from_name")),
      $this->resource->email,
      "Your order from $site_title",
      $body
    );
    sfContext::getInstance()->getMailer()->send($message);
  }


  public function refundResources($refund_resources, $note) 
  {
    $refund_ids = array();
    $total = '0';
    foreach ($this->resource->saleResources as $saleResource) {
      if (in_array($saleResource->resource->getId(), $refund_resources)) {
        $total = bcadd($total, $saleResource['price'], 4);
        $refund_ids[] = $saleResource->resource->getId();
      }
    }
    $taxes = sfEcommercePlugin::calculate_taxes_on_resources($this->resource, $refund_resources);
    foreach ($taxes as $taxname => $taxamount) {
      $total = bcadd($total, $taxamount, 4);
    }

    // is it a full refund or partial?
    if (count($this->resource->saleResources) == count($refund_ids)){
      $type = 'Full';
    } else {
      $type = 'Partial';
    }

    $req = "USER=" . sfConfig::get("ecommerce_paypal_api_username") .
           "&PWD=" . sfConfig::get("ecommerce_paypal_api_password") .
           "&SIGNATURE=" . sfConfig::get("ecommerce_paypal_api_signature") .
           "&METHOD=RefundTransaction" .
           "&VERSION=94" .
           "&TRANSACTIONID=" . $this->resource['transactionId'] .
           "&REFUNDTYPE=" . $type;

    if ($type == 'Partial') {
      $req .= "&AMT=" . sfEcommercePlugin::bcround($total, 2);
      $req .= "&CURRENCYCODE=CAD";
      $req .= "&NOTE=" . $note;
    }

    $this->logMessage("Sending refund: " . $req, 'notice');
    $this->logMessage(sfConfig::get("ecommerce_paypal_api_url"), 'notice');

    $ch = curl_init(sfConfig::get("ecommerce_paypal_api_url"));
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

    // In wamp-like environments that do not come bundled with root authority certificates,
    // please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set 
    // the directory path of the certificate as shown below:
    // curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
    if( !($res = curl_exec($ch)) ) {
        $this->logMessage("Got " . curl_error($ch) . " when sending PayPal Refund.", 'notice');
        curl_close($ch);
        exit;
    }
    curl_close($ch);
    $this->logMessage("PayPal response " . $res, 'notice');

    parse_str($res, $result_array);

    // mark which saleResources are pending refund
    foreach ($this->resource->saleResources as $saleResource) {
      if (in_array($saleResource->resource->getId(), $refund_resources)) {
        $refund_ids[] = $saleResource->resource->getId();
        $saleResource['processingStatus'] = 'pending_refund';
        $saleResource['refundTransactionId'] = $result_array['REFUNDTRANSACTIONID'];
        $saleResource->save();
      }
    }
  }
}
