<?php


/**
 * Skeleton subclass for representing a row from the 'ecommerce_transaction' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    plugins.sfEcommercePlugin.lib.model
 */
class QubitEcommerceTransaction extends BaseEcommerceTransaction {

	/**
	 * Initializes internal state of QubitEcommerceTransaction object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // QubitEcommerceTransaction
