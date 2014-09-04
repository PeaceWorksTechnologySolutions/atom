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

class sfEcommercePluginSalesReportAction extends sfAction
{
  public function execute($request)
  {
    if (!isset($request->limit))
    {
      $request->limit = 10000; // we want to show all records on one page, for easy printing.
    }

    if (!isset($request->sort))
    {
      $request->sort = sfConfig::get('app_sort_browser_user');
    }

    $this->stats = $this->get_stats($request);

    // Page results
    $criteria = $this->build_criteria($request);
    $this->pager = new QubitPager('QubitEcommerceTransaction');
    $this->pager->setCriteria($criteria);
    $this->pager->setMaxPerPage($request->limit);
    $this->pager->setPage($request->page);
  }


  public function build_criteria($request)
  {
    $criteria = new Criteria;
    $criteria->add(QubitUserEcommerceSettings::USER_ID, $this->getUser()->user->getId());
    $settings = QubitUserEcommerceSettings::get($criteria)->__get(0);
    $this->user_repo = $settings->repository->getId();

    $criteria = new Criteria;
    $criteria->add(QubitEcommerceTransaction::REPOSITORY_ID, $this->user_repo, Criteria::EQUAL);

    $this->start_date = $request->start_date;
    if (!empty($request->start_date)) {
      $criteria->add(QubitEcommerceTransaction::CREATED_AT, $request->start_date, Criteria::GREATER_EQUAL);
    }
    $this->end_date = $request->end_date;
    if (!empty($request->end_date)) {
      $criteria->addAnd(QubitEcommerceTransaction::CREATED_AT, $request->end_date . " 23:59:59", Criteria::LESS_EQUAL);
    }
    return $criteria;
  }

  public function get_stats($request)
  {
    $criteria = $this->build_criteria($request);
    $criteria->addSelectColumn('SUM(amount) as net_total');
    $criteria->addSelectColumn('count(*) as num_transactions');
    $criteria->addSelectColumn('count(distinct(sale_id)) as total_orders');
    $result = BasePeer::doSelect($criteria)->fetchAll(PDO::FETCH_ASSOC);

    $stats = $result[0];

    $criteria = $this->build_criteria($request);
    $criteria->addSelectColumn('SUM(amount) as gross_sales');
    $criteria->add(QubitEcommerceTransaction::TYPE, '%fee%', Criteria::NOT_LIKE);
    $result = BasePeer::doSelect($criteria)->fetchAll(PDO::FETCH_ASSOC);
    $stats = array_merge($stats, $result[0]);

    $criteria = $this->build_criteria($request);
    $criteria->addSelectColumn('SUM(amount) as item_sales');
    $criteria->add(QubitEcommerceTransaction::TYPE, 'sale');
    $criteria->addOr(QubitEcommerceTransaction::TYPE, 'refund');
    $result = BasePeer::doSelect($criteria)->fetchAll(PDO::FETCH_ASSOC);
    $stats = array_merge($stats, $result[0]);

    $criteria = $this->build_criteria($request);
    $criteria->addSelectColumn('-SUM(amount) as fees');
    $criteria->add(QubitEcommerceTransaction::TYPE, '%fee%', Criteria::LIKE);
    $result = BasePeer::doSelect($criteria)->fetchAll(PDO::FETCH_ASSOC);
    $stats = array_merge($stats, $result[0]);

    $criteria = $this->build_criteria($request);
    $criteria->addJoin(QubitEcommerceTransaction::SALE_ID, QubitSaleResource::SALE_ID);
    $criteria->add(QubitEcommerceTransaction::TYPE, 'sale');
    $criteria->add(QubitSaleResource::PROCESSING_STATUS, '%refund%', Criteria::NOT_LIKE);
    $criteria->add(QubitSaleResource::REPOSITORY_ID, $this->user_repo, Criteria::EQUAL);
    $criteria->addSelectColumn('count(*) as photos_sold');
    $result = BasePeer::doSelect($criteria)->fetchAll(PDO::FETCH_ASSOC);
    $stats = array_merge($stats, $result[0]);

    $criteria = $this->build_criteria($request);
    $criteria->add(QubitEcommerceTransaction::TYPE, 'tax %', Criteria::LIKE);
    $criteria->addGroupByColumn('type');
    $criteria->addSelectColumn('SUM(amount) as tax_total');
    $criteria->addSelectColumn('type');
    $result = BasePeer::doSelect($criteria)->fetchAll(PDO::FETCH_ASSOC);
    $stats['taxes'] = array();
    foreach ($result as $row) {
      // type contains something like 'refund tax GST' or 'tax PST'
      // in either case the tax name is at the end
      $parts = explode(' ', $row['type']);
      $taxname = $parts[count($parts) - 1];
      if (!isset($stats['taxes'][$taxname])) {
        $stats['taxes'][$taxname] = '0';
      }
      $stats['taxes'][$taxname] = bcadd($stats['taxes'][$taxname], $row['tax_total'], 2);
    }
    return $stats;
  }

}
