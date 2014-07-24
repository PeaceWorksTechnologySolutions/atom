<?php


/**
 * This class defines the structure of the 'sale' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    plugins.sfEcommercePlugin.lib.model.map
 */
class SaleTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.sfEcommercePlugin.lib.model.map.SaleTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('sale');
		$this->setPhpName('sale');
		$this->setClassname('QubitSale');
		$this->setPackage('plugins.sfEcommercePlugin.lib.model');
		$this->setUseIdGenerator(false);
		// columns
		$this->addForeignPrimaryKey('ID', 'id', 'INTEGER' , 'object', 'ID', true, null, null);
		$this->addColumn('FIRST_NAME', 'firstName', 'VARCHAR', false, 50, null);
		$this->addColumn('LAST_NAME', 'lastName', 'VARCHAR', false, 50, null);
		$this->addColumn('ADDRESS1', 'address1', 'VARCHAR', false, 50, null);
		$this->addColumn('ADDRESS2', 'address2', 'VARCHAR', false, 50, null);
		$this->addColumn('CITY', 'city', 'VARCHAR', false, 50, null);
		$this->addColumn('COUNTRY', 'country', 'VARCHAR', false, 2, null);
		$this->addColumn('PROVINCE', 'province', 'VARCHAR', false, 30, null);
		$this->addColumn('POSTAL_CODE', 'postalCode', 'VARCHAR', false, 15, null);
		$this->addColumn('EMAIL', 'email', 'VARCHAR', false, 50, null);
		$this->addColumn('PHONE', 'phone', 'VARCHAR', false, 30, null);
		$this->addColumn('PROCESSING_STATUS', 'processingStatus', 'VARCHAR', false, 50, null);
		$this->addColumn('TOTAL_AMOUNT', 'totalAmount', 'VARCHAR', false, 20, null);
		$this->addColumn('PAID_AT', 'paidAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('TRANSACTION_ID', 'transactionId', 'VARCHAR', false, 200, null);
		$this->addColumn('TRANSACTION_FEE', 'transactionFee', 'VARCHAR', false, 20, null);
		$this->addColumn('TRANSACTION_DATE', 'transactionDate', 'VARCHAR', false, 50, null);
		$this->addColumn('CREATED_AT', 'createdAt', 'TIMESTAMP', true, null, null);
		$this->addColumn('UPDATED_AT', 'updatedAt', 'TIMESTAMP', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('object', 'object', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
    $this->addRelation('saleResource', 'saleResource', RelationMap::ONE_TO_MANY, array('id' => 'sale_id', ), 'CASCADE', null);
	} // buildRelations()

} // SaleTableMap
