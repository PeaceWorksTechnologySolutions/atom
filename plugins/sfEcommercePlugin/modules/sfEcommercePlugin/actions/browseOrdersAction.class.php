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

class sfEcommercePluginBrowseOrdersAction extends sfAction
{
  public function execute($request)
  {
    if (!isset($request->limit))
    {
      $request->limit = sfConfig::get('app_hits_per_page');
    }

    if (!isset($request->sort))
    {
      $request->sort = sfConfig::get('app_sort_browser_user');
    }

    $criteria = new Criteria;
    $criteria->add(QubitUserEcommerceSettings::USER_ID, $this->getUser()->user->getId());
    $settings = QubitUserEcommerceSettings::get($criteria)->__get(0);
    $user_repo = $settings->repository->getId();

    $criteria = new Criteria;
    $subselect = "sale.id in (select sale_id from sale_resource sr where sr.sale_id = sale.id and sr.repository_id = " . $user_repo;
    if (!isset($request->filter) || $request->filter == 'paid') {
      $subselect .= " and sr.processing_status = 'new' ";
    }
    $subselect .= ")";
    $criteria->add(QubitSale::ID, $subselect, Criteria::CUSTOM);

    if (!isset($request->filter) || $request->filter == 'paid') {
      $criteria->add(QubitSale::PROCESSING_STATUS, 'paid', Criteria::EQUAL);
      $this->selected_filter = 'paid';
    } else if ($request->filter == 'all') {
      $criteria->add(QubitSale::PROCESSING_STATUS, 'pending_payment', Criteria::NOT_EQUAL);
      $this->selected_filter = 'all';
    } else {
      $criteria->add(QubitSale::PROCESSING_STATUS, $request->filter, Criteria::EQUAL);
      $this->selected_filter = $request->filter;
    }

    $this->filter_options = array('all', 'paid', 'refunded', 'cancelled', 'processed');

    if (isset($request->subquery))
    {
      // search for order # (if numeric), or for customer name
      if (is_numeric($request->subquery)) {
        $criteria->addAnd(QubitSale::ID, (int)$request->subquery);
      } else {
        $conn = Propel::getConnection();
        $subquery = "%" . $request->subquery . "%";
        $sql = "lower(concat(sale.first_name, ' ', sale.last_name)) like lower(" . $conn->quote($subquery) . ")";
        $criteria->addAnd(QubitSale::ID, $sql, Criteria::CUSTOM);
      }
    }

    switch ($request->sort)
    {
      case 'alphabetic':
        $criteria->addAscendingOrderByColumn('authorized_form_of_name');
        break;

      case 'lastUpdated':
      default:
        $criteria->addDescendingOrderByColumn(QubitObject::UPDATED_AT);

        break;
    }

    // Page results
    $this->pager = new QubitPager('QubitSale');
    $this->pager->setCriteria($criteria);
    $this->pager->setMaxPerPage($request->limit);
    $this->pager->setPage($request->page);
  }
}
