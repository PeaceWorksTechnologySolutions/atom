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

class sfEcommercePluginIPNAction extends sfEcommercePaymentAction
{
  public static $check_user = false;

  public function execute($request)
  {
    parent::execute($request);

    $this->logMessage('ipn received', 'notice');

    $verification_response = $this->verify_ipn($request);
    if (strcmp ($verification_response, "VERIFIED") == 0) {
      $this->logMessage('ipn was verified successfully', 'notice');
    } else {
      $this->logMessage('ipn was not verified - got ' . $verification_response, 'notice');
      $context = sfContext::getInstance()->getResponse()->setStatusCode(403);
      throw new sfSecurityException;
    }

    if (!$request->isMethod('post')) {
      $this->logMessage('ipn received - method was not post', 'notice');
      $context = sfContext::getInstance()->getResponse()->setStatusCode(403);
      throw new sfSecurityException;
    }
    $fields = $request->getPostParameters();
    $this->logMessage(var_export($fields, true), 'notice');

    if (intval($fields['invoice']) != $this->resource->getId()) {
      $this->logMessage("invoice '" . $fields['invoice'] . "' does not match - aborting.", 'notice');
      $context = sfContext::getInstance()->getResponse()->setStatusCode(403);
      throw new sfSecurityException;
    }

    if ($fields['business'] != sfConfig::get("ecommerce_paypal_email")) {
      $this->logMessage("Received IPN with incorrect business '" . $fields['business'] . "' - aborting.", 'notice');
      $context = sfContext::getInstance()->getResponse()->setStatusCode(403);
      throw new sfSecurityException;
    }

    if ($fields['mc_currency'] != 'CAD') {
      $this->logMessage("Received payment in currency " . $fields['mc_currency'] . " but expected CAD - aborting.'", 'notice');
      $context = sfContext::getInstance()->getResponse()->setStatusCode(403);
      throw new sfSecurityException;
    }

    if ($fields['payment_status'] == 'Completed') {
      // we've received a payment.

      if ($this->resource['processingStatus'] != 'pending_payment') {
        $this->logMessage("Received 'Completed' IPN for order which does not have status 'pending_payment'", 'notice');
        $context = sfContext::getInstance()->getResponse()->setStatusCode(403);
        throw new sfSecurityException;
      }
      if (floatval($this->resource['totalAmount']) != floatval($fields['mc_gross'])) {
        $this->logMessage("Received " . floatval($fields['mc_gross']) . " but expected " . floatval($this->resource['totalAmount']) . " - aborting.'", 'notice');
        $context = sfContext::getInstance()->getResponse()->setStatusCode(403);
        throw new sfSecurityException;
      }

      // process the payment
      $this->resource['processingStatus'] = 'paid';
      $this->resource['transactionId'] = $fields['txn_id'];
      $this->resource['transactionFee'] = $fields['mc_fee'];
      $this->resource['transactionDate'] = $fields['payment_date'];
      $this->resource['paidAt'] = date('Y-m-d H:i:s');
      $this->resource->save();


    } elseif ($fields['payment_status'] == 'Refunded') {
      // we've received a refund
      if ($this->resource['transactionId'] != $fields['parent_txn_id']) {
        $this->logMessage("Received 'Refunded' IPN but txn_id does not match", 'notice');
        $context = sfContext::getInstance()->getResponse()->setStatusCode(403);
        throw new sfSecurityException;
      }
      // mark the saleResources which have been successfully refunded.
      foreach ($this->resource->saleResources as $saleResource) {
        if ($saleResource['processingStatus'] == 'pending_refund' 
        && $saleResource['refundTransactionId'] == $fields['txn_id']) {
          $saleResource['processingStatus'] = 'refunded';
          $saleResource->save();
        }
      }

      $all_refunded = true;
      foreach ($this->resource->saleResources as $saleResource) {
        if ($saleResource['processingStatus'] != 'refunded') {
          $all_refunded = false;
          break;
        }
      }
      if ($all_refunded) {
        $this->resource['processingStatus'] = 'refunded';
        $this->resource->save();
      }

    } else {
      $this->logMessage("Received IPN with status '" . $fields['payment_status'] . "' which is not handled - aborting.", 'notice');
      $context = sfContext::getInstance()->getResponse()->setStatusCode(403);
      throw new sfSecurityException;
    }

  }

  public function verify_ipn($request) {
    // Code from https://gist.github.com/xcommerce-gists/3440401#file-completelistener-php
    $raw_post_data = file_get_contents('php://input');
    $raw_post_array = explode('&', $raw_post_data);
    $myPost = array();
    foreach ($raw_post_array as $keyval) {
      $keyval = explode ('=', $keyval);
      if (count($keyval) == 2)
         $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
    // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
    $req = 'cmd=_notify-validate';
    if(function_exists('get_magic_quotes_gpc')) {
       $get_magic_quotes_exists = true;
    } 
    foreach ($myPost as $key => $value) {        
       if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
            $value = urlencode(stripslashes($value)); 
       } else {
            $value = urlencode($value);
       }
       $req .= "&$key=$value";
    }
     
    // STEP 2: POST IPN data back to PayPal to validate
     
    $ch = curl_init(sfConfig::get("ecommerce_paypal_url"));
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
        $this->logMessage("Got " . curl_error($ch) . " when verifying IPN data.", 'notice');
        curl_close($ch);
        exit;
    }
    curl_close($ch);
    return $res;
  }


}
