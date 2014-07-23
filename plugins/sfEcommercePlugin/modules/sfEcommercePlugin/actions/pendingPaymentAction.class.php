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

class sfEcommercePluginPendingPaymentAction extends sfEcommercePaymentAction
{
  public function execute($request)
  {
    parent::execute($request);

    // redirect if payment has completed
    if ($this->resource['processingStatus'] == 'paid') {
      // clear the user's cart
      $this->getUser()->setAttribute('cart_contents', array());

      $this->redirect(array('module' => 'staticpage', 'slug' => 'paymentComplete'));
    }
    
    // otherwise wait up to 120 seconds until status changed to paid. 
    $created_timestamp = strtotime($this->resource['createdAt']);
    $current_timestamp = strtotime(date('Y-m-d H:i:s'));
    $age = $current_timestamp - $created_timestamp;
    if ($this->resource['processingStatus'] == 'pending_payment' && $age >= 120) {
      $this->contact_for_support = true;
    } else {
      $this->contact_for_support = false;
    }
  }
}
