<?php

/** The Collabra Market Project
  *   Part of the Collabra Commercial Collaboration Platform
  *
  * Copyright(c) 2011 Theodore R. Smith <theodore@phpexperts.pro>
  * All rights reserved.
 **/
 
class Market
{
	private function __construct() { }
	public static function init()
	{
		spl_autoload_register(array(__CLASS__, 'includeFiles'));
		self::includeFiles();
	}
	
	public static function includeFiles()
	{
		if (defined('CMARKET_LIB_PATH'))
		{
			// It seems the library has already been initialized; bail.
			return;
		}

		// TODO: Change CMARKET_LIB_PATH to CMARKET_PATH.
		define('COLLABRA_PATH', realpath(dirname(__FILE__) . '/../'));
		define('CMARKET_LIB_PATH', dirname(__FILE__));

		// Init the Flourish library.
		include COLLABRA_PATH . '/lib/flourish/Flourish.php';
		Flourish::init();

		include CMARKET_LIB_PATH . '/api/Command.interface.php';
		include CMARKET_LIB_PATH . '/api/View.interface.php';

		include CMARKET_LIB_PATH . '/models/Commodity.datatype.php';
		include CMARKET_LIB_PATH . '/models/CommodityStore.datatype.php';
		include CMARKET_LIB_PATH . '/models/ActionsList.datatype.php';

		include CMARKET_LIB_PATH . '/commodities/CommoditiesBasket.class.php';
		include CMARKET_LIB_PATH . '/commodities/CommoditiesExchange.class.php';
		include CMARKET_LIB_PATH . '/commodities/CommoditiesFactory.class.php';

		include CMARKET_LIB_PATH . '/controllers/ControllerCommander.class.php';
		include CMARKET_LIB_PATH . '/controllers/Generic.class.php';
		include CMARKET_LIB_PATH . '/controllers/Loan.class.php';
		include CMARKET_LIB_PATH . '/controllers/Payment.class.php';

		include CMARKET_LIB_PATH . '/managers/Payment.class.php';
		include CMARKET_LIB_PATH . '/managers/Loan.class.php';
	}
}

