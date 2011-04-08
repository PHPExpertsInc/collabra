<?php

// Monica Chase (monica@phpexperts.pro) | 4 April 2011
// Copyright (c) 2011 Theodore R. Smith <theodore@phpexperts.pro> 
// Uses the 
class CommodityExchange
{
	private static $instance;

	private function __construct()
	{
	}

	public static function getInstance ()
	{
		if(is_null($this->instance))
		{
			$this->instance = new CommodityExchange;
		}
		return $this->instance;
	}

	public function exchange(CommodityBasket $inputBasket, CommodityBasket $deliverableBasket)
	{
		// 1. Obtain the valuation difference for the two commodities.
		$difference = self::calculateValueDifferential($inputBasket, $deliverableBasket);

		// 2. Return the results from $this->handleValueDifference().
		return $this->handleValueDifference($difference);
	}

	/** @return float The valuation difference between two commodities **/
	public static function calculateValueDifferential(CommodityBasket $inputBasket, CommodityBasket $deliverableBasket)
	{
		$difference = $inputBasket->currentValuation - $deliverableBasket->currentValuation;

		return $difference;
	}
	
	/** @return CommodityBasket Returns the difference as Federal Reserve Notes.**/
	protected function handleValueDifference($difference)
	{
		if ($difference < 0)
		{
			throw new CommodityException("INSUFFICIENT FUNDS: Input is worth less than deliverable.");
		}

		$frn2 = CommoditiesFactory::build("Federal Reserve Note");
		$frn2->currentValuation = $difference;
		return $frn2;
	}

	/* Statistical getter */
	public function fetchValueDifference(CommodityBasket $inputBasket, CommodityBasket $deliverableBasket)
	{
		return $this->calculateValueDifference($inputBasket, $deliverableBasket);
	}
	
}
