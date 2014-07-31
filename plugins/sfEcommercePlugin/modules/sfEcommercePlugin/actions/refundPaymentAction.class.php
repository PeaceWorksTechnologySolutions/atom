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

class sfEcommercePluginRefundPaymentAction extends sfEcommercePaymentAction
{
  public static $check_user = false;  // FIXME: remove this when done testing

  public function execute($request)
  {
    parent::execute($request);

    $this->refundResources(array(446), 'refund another item');
  }


  public function refundResources($refund_resources, $note) 
  {
    $refund_ids = array();
    $total = 0;
    foreach ($this->resource->saleResources as $saleResource) {
      if (in_array($saleResource->resource->getId(), $refund_resources)) {
        $total += floatval($saleResource['price']);
        $refund_ids[] = $saleResource->resource->getId();
      }
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
      $req .= "&AMT=" . number_format(round($total, 2), 2, ".", "");
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
