<?php


/**
 * This class defines the structure of the 'sale_resource' table.
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
class SaleResourceTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.sfEcommercePlugin.lib.model.map.SaleResourceTableMap';

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
		$this->setName('sale_resource');
		$this->setPhpName('saleResource');
		$this->setClassname('QubitSaleResource');
		$this->setPackage('plugins.sfEcommercePlugin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'id', 'INTEGER', true, null, null);
		$this->addForeignKey('SALE_ID', 'saleId', 'INTEGER', 'sale', 'ID', false, null, null);
		$this->addForeignKey('RESOURCE_ID', 'resourceId', 'INTEGER', 'information_object', 'ID', false, null, null);
		$this->addForeignKey('REPOSITORY_ID', 'repositoryId', 'INTEGER', 'repository', 'ID', false, null, null);
		$this->addColumn('PRICE', 'price', 'VARCHAR', false, 20, null);
		$this->addColumn('PROCESSING_STATUS', 'processingStatus', 'VARCHAR', false, 50, null);
		$this->addColumn('REFUND_TRANSACTION_ID', 'refundTransactionId', 'VARCHAR', false, 50, null);
		$this->addColumn('SERIAL_NUMBER', 'serialNumber', 'INTEGER', true, null, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('sale', 'sale', RelationMap::MANY_TO_ONE, array('sale_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('informationObject', 'informationObject', RelationMap::MANY_TO_ONE, array('resource_id' => 'id', ), null, null);
    $this->addRelation('repository', 'repository', RelationMap::MANY_TO_ONE, array('repository_id' => 'id', ), null, null);
	} // buildRelations()

} // SaleResourceTableMap
