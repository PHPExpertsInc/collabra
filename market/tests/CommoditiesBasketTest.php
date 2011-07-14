<?php

/** The Collabra Market Project
  *   Part of the Collabra Commercial Collaboration Platform
  *
  * Copyright(c) 2011 Theodore R. Smith <theodore@phpexperts.pro>
  * All rights reserved.
 **/

require_once 'bootstrap.inc.php';

require_once 'PHPUnit/Framework/TestCase.php';

class CommoditiesBasketTest extends PHPUnit_Framework_TestCase
{
	/** @var CommodityStore **/
	private $basket;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		$this->basket = new CommoditiesBasket;
		parent::setUp();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
	}

	/** @return Commodity **/
	protected function makeACommodity($name = "Test Commodity", $cv = 1.5, $av = 1.5)
	{
		$commodity = new Commodity;
		$commodity->name = $name;
		$commodity->currentValuation = $cv;
		$commodity->averageValuation = $av;

		return $commodity;
	}

	public function testStartsOutEmpty()
	{
		$this->assertAttributeEquals(null, 'commoditiesQueue', $this->basket);
	}

	public function testCanAddACommodity()
	{
		$commodity = $this->makeACommodity();

		$this->basket->add($commodity, 2);

		// Make sure that only one item has been added, with quantity of 2.
		$expectedResults = array($commodity->name => new CommodityStore($commodity, 2));
		$this->assertAttributeEquals($expectedResults, 'commoditiesQueue', $this->basket);
	}

	public function testWillAddOneCommodityByDefault()
	{
		$commodity = $this->makeACommodity();

		$this->basket->add($commodity);

		// Make sure that only one item has been added, with quantity of 1.
		$expectedResults = array($commodity->name => new CommodityStore($commodity, 1));
		$this->assertAttributeEquals($expectedResults, 'commoditiesQueue', $this->basket);
	}

	public function testCannotAddACommodityWithAnNonNumericQuantity()
	{
		$commodity = $this->makeACommodity();
		try
		{
			$this->basket->add($commodity, 'Non-Numeric Quantity');
			$this->fail('Accepted an invalid quantity.');
		}
		catch(InvalidArgumentException $e)
		{
			$this->assertEquals("Quantity must be numerical.", $e->getMessage());
		}
	}

	public function testAddingTheSameCommodityWillIncreaseItsQuantity()
	{
		$commodity = $this->makeACommodity();

		$this->basket->add($commodity);
		$this->basket->add($commodity, 2);

		// Make sure that only one item has been added, with quantity of 1.
		$expectedResults = array($commodity->name => new CommodityStore($commodity, 3));
		$this->assertAttributeEquals($expectedResults, 'commoditiesQueue', $this->basket);
	}

	public function testMultipleCommoditiesCanBeAdded()
	{
		$commodity = $this->makeACommodity();
		$commodity2 = $this->makeACommodity('Test 2', 2, 2);

		$this->basket->add($commodity);
		$this->basket->add($commodity2, 2);

		// Make sure that only one item has been added, with quantity of 1.
		$expectedResults = array($commodity->name => new CommodityStore($commodity, 1),
		                         $commodity2->name => new CommodityStore($commodity2, 2));
		$this->assertAttributeEquals($expectedResults, 'commoditiesQueue', $this->basket);
	}

	public function testTakingFromAnEmptyBasketWontWork()
	{
		try
		{
			$this->basket->take();
			// Test should NOT get this far!
			$this->assert(false);
		}
		catch (CommodityException $e)
		{
			if ($e->getMessage() != "Your basket is empty")
			{
				$this->assert(false);
			}
		}
		catch (Exception $e)
		{
			$this->assert(false);
		}
	}

	public function testTakesFirstCommodityFromTheBasket()
	{
		// Add two things to the Basket.
		$this->testMultipleCommoditiesCanBeAdded();

		// Set up the test data.
		$commodity = $this->makeACommodity();
		$expectedCommodityStore = new CommodityStore($commodity, 1);

		// Confirm it's the right commodity store.
		$commodityStore = $this->basket->take();
		$this->assertEquals($expectedCommodityStore, $commodityStore);

		// Confirm the commodity store has been removed from the Basket.
		$commodity2 = $this->makeACommodity('Test 2', 2, 2);
		$expectedResults = array($commodity2->name => new CommodityStore($commodity2, 2));
		$this->assertAttributeEquals($expectedResults, 'commoditiesQueue', $this->basket);
	}

	public function testCanRetrieveASpecificCommodity()
	{
		// Add two things to the Basket.
		$this->testMultipleCommoditiesCanBeAdded();

		// Set up the test data.
		$commodity = $this->makeACommodity();
		$expectedCommodityStore = new CommodityStore($commodity, 1);

		// Confirm it's the right commodity store.
		$commodityStore = $this->basket->fetchCommodity($commodity->name);
		$this->assertEquals($expectedCommodityStore, $commodityStore);
	}

	public function testWillThrowExceptionOnMissingCommodity()
	{
		try
		{
			$this->basket->fetchCommodity('Unknown commodity');
			$this->fail('Tried to fetch an unknown commodity.');
		}
		catch(CommodityException $e)
		{
			$this->assertEquals("Commodity Not Found", $e->getMessage());
		}
	}

	public function testReturnsZeroWorthForEmptyBaskets()
	{
		$value = $this->basket->getTotalValuation();
		$this->assertEquals(0, $value);
	}

	public function testReturnsProperWorthOfACommodity()
	{
		// Add one thing to the Basket.
		$commodity = $this->makeACommodity();
		$this->basket->add($commodity, 3);

		// Set up the test data.
		$commodityStore = new CommodityStore($commodity, 3);
		$expectedValue = $commodityStore->calculateWorth();

		// Confirm it's the right commodity store.
		$value = $this->basket->getTotalValuation();
		$this->assertEquals($expectedValue, $value);

	}

	public function testWillAccuratelyReturnStatistics()
	{
/*
             $stats[] = array('name'      => $commodityName,
                              'valuation' => $store->commodity->currentValuation,
                              'quantity'  => $store->quantity,
                              'subtotal'  => $store->calculateWorth());
*/
		// Add two things to the Basket.
		$this->testMultipleCommoditiesCanBeAdded();

		// Set up the test data.
		$expectedStats[] = array('name'      => 'Test Commodity',
		                         'valuation' => 1.5,
		                         'quantity'  => 1,
		                         'subtotal'  => 1.5);

		$expectedStats[] = array('name'      => 'Test 2',
		                         'valuation' => 2,
		                         'quantity'  => 2,
		                         'subtotal'  => 4.0);
		$this->assertEquals($expectedStats, $this->basket->dumpStats());

//		$this->asserts(false);
	}

}

