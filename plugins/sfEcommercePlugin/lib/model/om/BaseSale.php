<?php

abstract class BaseSale extends QubitObject implements ArrayAccess
{
  const
    DATABASE_NAME = 'propel',

    TABLE_NAME = 'sale',

    ID = 'sale.ID',
    FIRST_NAME = 'sale.FIRST_NAME',
    LAST_NAME = 'sale.LAST_NAME',
    ADDRESS1 = 'sale.ADDRESS1',
    ADDRESS2 = 'sale.ADDRESS2',
    CITY = 'sale.CITY',
    COUNTRY = 'sale.COUNTRY',
    PROVINCE = 'sale.PROVINCE',
    POSTAL_CODE = 'sale.POSTAL_CODE',
    EMAIL = 'sale.EMAIL',
    PHONE = 'sale.PHONE',
    PROCESSING_STATUS = 'sale.PROCESSING_STATUS',
    TOTAL_AMOUNT = 'sale.TOTAL_AMOUNT',
    PAID_AT = 'sale.PAID_AT',
    TRANSACTION_ID = 'sale.TRANSACTION_ID',
    TRANSACTION_FEE = 'sale.TRANSACTION_FEE',
    TRANSACTION_DATE = 'sale.TRANSACTION_DATE',
    CREATED_AT = 'sale.CREATED_AT',
    UPDATED_AT = 'sale.UPDATED_AT',
    LAST_PROCESSED_AT = 'sale.LAST_PROCESSED_AT';

  public static function addSelectColumns(Criteria $criteria)
  {
    parent::addSelectColumns($criteria);

    $criteria->addJoin(QubitSale::ID, QubitObject::ID);

    $criteria->addSelectColumn(QubitSale::ID);
    $criteria->addSelectColumn(QubitSale::FIRST_NAME);
    $criteria->addSelectColumn(QubitSale::LAST_NAME);
    $criteria->addSelectColumn(QubitSale::ADDRESS1);
    $criteria->addSelectColumn(QubitSale::ADDRESS2);
    $criteria->addSelectColumn(QubitSale::CITY);
    $criteria->addSelectColumn(QubitSale::COUNTRY);
    $criteria->addSelectColumn(QubitSale::PROVINCE);
    $criteria->addSelectColumn(QubitSale::POSTAL_CODE);
    $criteria->addSelectColumn(QubitSale::EMAIL);
    $criteria->addSelectColumn(QubitSale::PHONE);
    $criteria->addSelectColumn(QubitSale::PROCESSING_STATUS);
    $criteria->addSelectColumn(QubitSale::TOTAL_AMOUNT);
    $criteria->addSelectColumn(QubitSale::PAID_AT);
    $criteria->addSelectColumn(QubitSale::TRANSACTION_ID);
    $criteria->addSelectColumn(QubitSale::TRANSACTION_FEE);
    $criteria->addSelectColumn(QubitSale::TRANSACTION_DATE);
    $criteria->addSelectColumn(QubitSale::CREATED_AT);
    $criteria->addSelectColumn(QubitSale::UPDATED_AT);
    $criteria->addSelectColumn(QubitSale::LAST_PROCESSED_AT);

    return $criteria;
  }

  public static function get(Criteria $criteria, array $options = array())
  {
    if (!isset($options['connection']))
    {
      $options['connection'] = Propel::getConnection(QubitSale::DATABASE_NAME);
    }

    self::addSelectColumns($criteria);

    return QubitQuery::createFromCriteria($criteria, 'QubitSale', $options);
  }

  public static function getAll(array $options = array())
  {
    return self::get(new Criteria, $options);
  }

  public static function getOne(Criteria $criteria, array $options = array())
  {
    $criteria->setLimit(1);

    return self::get($criteria, $options)->__get(0, array('defaultValue' => null));
  }

  public static function getById($id, array $options = array())
  {
    $criteria = new Criteria;
    $criteria->add(QubitSale::ID, $id);

    if (1 == count($query = self::get($criteria, $options)))
    {
      return $query[0];
    }
  }

  public function __construct()
  {
    parent::__construct();

    $this->tables[] = Propel::getDatabaseMap(QubitSale::DATABASE_NAME)->getTable(QubitSale::TABLE_NAME);
  }

  public function __isset($name)
  {
    $args = func_get_args();

    try
    {
      return call_user_func_array(array($this, 'QubitObject::__isset'), $args);
    }
    catch (sfException $e)
    {
    }

    if ('saleResources' == $name)
    {
      return true;
    }

    if ('ecommerceTransactions' == $name)
    {
      return true;
    }

    throw new sfException("Unknown record property \"$name\" on \"".get_class($this).'"');
  }

  public function __get($name)
  {
    $args = func_get_args();

    $options = array();
    if (1 < count($args))
    {
      $options = $args[1];
    }

    try
    {
      return call_user_func_array(array($this, 'QubitObject::__get'), $args);
    }
    catch (sfException $e)
    {
    }

    if ('saleResources' == $name)
    {
      if (!isset($this->refFkValues['saleResources']))
      {
        if (!isset($this->id))
        {
          $this->refFkValues['saleResources'] = QubitQuery::create();
        }
        else
        {
          $this->refFkValues['saleResources'] = self::getsaleResourcesById($this->id, array('self' => $this) + $options);
        }
      }

      return $this->refFkValues['saleResources'];
    }

    if ('ecommerceTransactions' == $name)
    {
      if (!isset($this->refFkValues['ecommerceTransactions']))
      {
        if (!isset($this->id))
        {
          $this->refFkValues['ecommerceTransactions'] = QubitQuery::create();
        }
        else
        {
          $this->refFkValues['ecommerceTransactions'] = self::getecommerceTransactionsById($this->id, array('self' => $this) + $options);
        }
      }

      return $this->refFkValues['ecommerceTransactions'];
    }

    throw new sfException("Unknown record property \"$name\" on \"".get_class($this).'"');
  }

  public static function addsaleResourcesCriteriaById(Criteria $criteria, $id)
  {
    $criteria->add(QubitSaleResource::SALE_ID, $id);

    return $criteria;
  }

  public static function getsaleResourcesById($id, array $options = array())
  {
    $criteria = new Criteria;
    self::addsaleResourcesCriteriaById($criteria, $id);

    return QubitSaleResource::get($criteria, $options);
  }

  public function addsaleResourcesCriteria(Criteria $criteria)
  {
    return self::addsaleResourcesCriteriaById($criteria, $this->id);
  }

  public static function addecommerceTransactionsCriteriaById(Criteria $criteria, $id)
  {
    $criteria->add(QubitEcommerceTransaction::SALE_ID, $id);

    return $criteria;
  }

  public static function getecommerceTransactionsById($id, array $options = array())
  {
    $criteria = new Criteria;
    self::addecommerceTransactionsCriteriaById($criteria, $id);

    return QubitEcommerceTransaction::get($criteria, $options);
  }

  public function addecommerceTransactionsCriteria(Criteria $criteria)
  {
    return self::addecommerceTransactionsCriteriaById($criteria, $this->id);
  }
}
