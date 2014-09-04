<?php


/**
 * This class defines the structure of the 'user_ecommerce_settings' table.
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
class UserEcommerceSettingsTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.sfEcommercePlugin.lib.model.map.UserEcommerceSettingsTableMap';

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
		$this->setName('user_ecommerce_settings');
		$this->setPhpName('userEcommerceSettings');
		$this->setClassname('QubitUserEcommerceSettings');
		$this->setPackage('plugins.sfEcommercePlugin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addForeignKey('USER_ID', 'userId', 'INTEGER', 'user', 'ID', false, null, null);
		$this->addForeignKey('REPOSITORY_ID', 'repositoryId', 'INTEGER', 'repository', 'ID', false, null, null);
		$this->addColumn('VACATION_ENABLED', 'vacationEnabled', 'BOOLEAN', false, null, false);
		$this->addColumn('VACATION_MESSAGE', 'vacationMessage', 'LONGVARCHAR', false, null, null);
		$this->addPrimaryKey('ID', 'id', 'INTEGER', true, null, null);
		$this->addColumn('SERIAL_NUMBER', 'serialNumber', 'INTEGER', true, null, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('user', 'user', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('repository', 'repository', RelationMap::MANY_TO_ONE, array('repository_id' => 'id', ), null, null);
	} // buildRelations()

} // UserEcommerceSettingsTableMap