<?php


/**
 * Skeleton subclass for representing a row from the 'user_ecommerce_settings' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    plugins.sfEcommercePlugin.lib.model
 */
class QubitUserEcommerceSettings extends BaseUserEcommerceSettings {

	/**
	 * Initializes internal state of QubitUserEcommerceSettings object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

  public function __get($name)
  {
    $args = func_get_args();

    $options = array();
    if (1 < count($args))
    {
      $options = $args[1];
    }

    switch ($name)
    {
      // render_field needs this object to have a sourceCulture attribute.
      case 'sourceCulture':
        return 'en';
    }
    return call_user_func_array(array($this, 'BaseUserEcommerceSettings::__get'), $args);
  }


} // QubitUserEcommerceSettings
