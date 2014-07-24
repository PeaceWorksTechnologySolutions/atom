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

class sfEcommercePaymentAction extends sfAction
{
  public static $check_user = true;

  public function execute($request)
  {
    // only allow user to access this page if it's their own sale
    $sale_id = $request->getParameter('id');

    if ($this::$check_user ) {
      $my_sales = $this->getUser()->getAttribute('my_sales', NULL);
      if (!in_array($sale_id, $my_sales)) {
        $context = sfContext::getInstance()->getResponse()->setStatusCode(403);
        throw new sfSecurityException;
      }
    }

    $criteria = new Criteria;
    $criteria->add(QubitObject::ID, $sale_id);
    $this->resource = QubitObject::get($criteria)->__get(0);
  }

  public function logMessage($message, $priority = 'info')
  {
    if (isset($this->resource)) {
      $message = "Order " . $this->resource->getId() . " " . $message;
      $message = str_replace("\n", " ", $message);
    }
    parent::logMessage($message, $priority);
  }
}
