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

class sfEcommercePluginPaymentAction extends sfEcommercePaymentAction
{
  public function execute($request)
  {
    parent::execute($request);

    // set up variables for the template
    $this->ipn_url = sfConfig::get("ecommerce_paypal_ipn_listener") . $this->getController()->genUrl(array('module' => 'sfEcommercePlugin', 'action' => 'ipn', 'id' => $this->resource->getId()), false);
    $this->pending_url = $this->getController()->genUrl(array('module' => 'sfEcommercePlugin', 'action' => 'pendingPayment', 'id' => $this->resource->getId()), true);
    $this->cancel_url = $this->getController()->genUrl(array('module' => 'sfEcommercePlugin', 'action' => 'cancelPayment', 'id' => $this->resource->getId()), true);

    $phone = preg_replace("/[^0-9]/", "", $this->resource['phone']);
    if (strlen($phone) == 10) {
      $this->phone = $phone;
    }

    $sale_items = array();
    $grand_total = '0';
    foreach ($this->resource->saleResources as $saleResource) {
      $repo = $saleResource->resource->getRepository(array('inherit' => true));
      $amount = sfEcommercePlugin::resource_price($saleResource->resource);
      $grand_total = bcadd($grand_total, $amount, 2);
      if (array_key_exists($repo->identifier, $sale_items)) {
        $sale_items[$repo->identifier]['quantity'] += 1;
        $sale_items[$repo->identifier]['total'] = bcmul($sale_items[$repo->identifier]['quantity'], $amount);
      } else {
        $sale_items[$repo->identifier] = array('quantity' => 1, 'amount' => $amount, 'total' => $amount, 'name' => $repo->authorizedFormOfName);
      }
    }
    $this->sale_items = $sale_items;

    // calculate and add taxes
    $tax_total = '0';
    $this->taxes = sfEcommercePlugin::calculate_taxes($this->resource);
    foreach ($this->taxes as $repo_identifier => $repo_taxes) {
      foreach ($repo_taxes as $taxname => $taxinfo) {
        $grand_total = bcadd($grand_total, $taxinfo['taxAmount'], 2);
        $tax_total = bcadd($tax_total, $taxinfo['taxAmount'], 2);
      }
    }

    $this->logMessage(var_export($sale_items, true), 'notice');

    $this->resource['totalAmount'] = sfEcommercePlugin::bcround($grand_total, 2);
    $this->taxAmount = sfEcommercePlugin::bcround($tax_total, 2);
    $this->resource->save();

  }
}
