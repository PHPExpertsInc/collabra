<?php
/** The Collabra Market Project
  *   Part of the Collabra Commercial Collaboration Platform
  *
  * Copyright(c) 2011 Theodore R. Smith <theodore@phpexperts.pro>
  * Copyright(c) 2011 Monica A. Chase <monica@phpexperts.pro>
  * All rights reserved.
 **/

/** 
  @package CollabraMarket
**/
class CommoditiesBasket
{
	// TODO: Determine a more descriptive name for array($commodity, $quantity)
	/** @var Commodity[] An array of Commodities **/
	private $commoditiesQueue;

	/** Adds a commodity to the basket.
	  * @param Commodity
	  * @param [int] Quantity of the commodity
	 **/
	public function add(Commodity $commodity, $quantity = 1)
	{
		if (!is_numeric($quantity))
		{
			throw new InvalidArgumentException("Quantity must be numerical.");
		}

		// The following is a powerful language construct called
		// "The terninary operator".

		// Use existing CommodityStore or create a new one with 0
		// quantity. (If quantity isn't 0, then you'll have 1 extra later.
		$store = isset($this->commoditiesQueue[$commodity->name])
			     ? $this->commoditiesQueue[$commodity->name]
				 : new CommodityStore($commodity, 0);

		$store->quantity += $quantity;
		$this->commoditiesQueue[$commodity->name] = $store;
	}
	
	/** @return CommodityStore **/
	public function take()
	{
		if(empty($this->commoditiesQueue))
		{
			throw new CommodityException("Your basket is empty");
		}

		$commodityStore = array_shift($this->commoditiesQueue);

		return $commodityStore;
	}

	/** dumpStats() returns the quantity, valuation, and total valuation of
	  * each commodity in the basket. 
	  *
	  * @return array('name', 'valuation', 'quantity', 'total')
	**/
	public function dumpStats()
	{
		// Loop through each array, performing calculations as needed.
		$stats = array();
		foreach ($this->commoditiesQueue as $commodityName => /** @var CommodityStore **/ $store)
		{
			$stats[] = array('name'      => $commodityName,
			                 'valuation' => $store->commodity->currentValuation,
			                 'quantity'  => $store->quantity,
			                 'subtotal'  => $store->calculateWorth());
		}

		return $stats;
	}

	/** fetchCommodity($commodityName) returns a specific commodity store, if available.
	  * @expect CommodityException[COMMODITY_NOT_FOUND]
	  * @return CommodityStore
	**/
	public function fetchCommodity($commodityName)
	{
		// 1. Throw a CommodityException if the commodity is not in the basket.
		if (!isset($this->commoditiesQueue[$commodityName]))
		{
			throw new CommodityException("Commodity Not Found");
		}

		// 2. Otherwise, return the commodity store.
		return $this->commoditiesQueue[$commodityName];
	}

	public function getTotalValuation()
	{
		$valuation = 0.00;

		if (empty($this->commoditiesQueue))
		{
			return 0.00;
		}

		foreach ($this->commoditiesQueue as 
				/** @var CommodityStore **/ $commodityStore)
		{
			$valuation += (float)$commodityStore->calculateWorth();
		}

		return $valuation;
	}
}















