<?php

namespace Trains\Test\Model;

use Trains\Model\Train;
use PHPUnit\Framework\TestCase;
use Util\StringUtil;

class TrainTest extends TestCase {
	
	/**
	 * @covers \Trains\Model\Train::fromArray
	 * @return void
	 */
	public function testFromArray() {
		$array = [
			"id" => random_int(0, PHP_INT_MAX),
			"line" => StringUtil::randomString(45),
			"route" => StringUtil::randomString(45),
			"run_number" => StringUtil::randomString(45),
			"operator_id" => StringUtil::randomString(45)
		];
		$train = Train::fromArray($array);
		
		$this->assertIsObject($train);
		$this->assertNotEmpty($train->getId());
		$this->assertNotEmpty($train->getLine());
		$this->assertNotEmpty($train->getRoute());
		$this->assertNotEmpty($train->getRunNumber());
		$this->assertNotEmpty($train->getOperatorId());
	}
	
	/**
	 * @covers \Trains\Model\Train::getLine
	 * @return void
	 */
	public function testGetLine() {
		$id = random_int(0, PHP_INT_MAX);
		$line = StringUtil::randomString(45);
		$route = StringUtil::randomString(45);
		$runNumber = StringUtil::randomString(45);
		$operatorId = StringUtil::randomString(45);
		$train = new Train($line, $route, $runNumber, $operatorId, $id);
		
		$this->assertIsString($train->getLine());
		$this->assertStringContainsString($line, $train->getLine());
	}
	
	/**
	 * @covers \Trains\Model\Train::getLine
	 * @return void
	 */
	public function testGetLineNull() {
		$train = new Train(null, null, null, null, null);
		
		$this->assertIsString($train->getLine());
	}
	
	/**
	 * @covers \Trains\Model\Train::getRoute
	 * @return void
	 */
	public function testGetRoute() {
		$id = random_int(0, PHP_INT_MAX);
		$line = StringUtil::randomString(45);
		$route = StringUtil::randomString(45);
		$runNumber = StringUtil::randomString(45);
		$operatorId = StringUtil::randomString(45);
		$train = new Train($line, $route, $runNumber, $operatorId, $id);
		
		$this->assertIsString($train->getRoute());
		$this->assertStringContainsString($route, $train->getRoute());
	}
	
	/**
	 * @covers \Trains\Model\Train::getRoute
	 * @return void
	 */
	public function testGetRouteeNull() {
		$train = new Train(null, null, null, null, null);
		
		$this->assertIsString($train->getRoute());
	}
	
	/**
	 * @covers \Trains\Model\Train::getRunNumber
	 * @return void
	 */
	public function testGetRunNumber() {
		$id = random_int(0, PHP_INT_MAX);
		$line = StringUtil::randomString(45);
		$route = StringUtil::randomString(45);
		$runNumber = StringUtil::randomString(45);
		$operatorId = StringUtil::randomString(45);
		$train = new Train($line, $route, $runNumber, $operatorId, $id);
		
		$this->assertIsString($train->getRunNumber());
		$this->assertStringContainsString($runNumber, $train->getRunNumber());
	}
	
	/**
	 * @covers \Trains\Model\Train::getRunNumber
	 * @return void
	 */
	public function testGetRunNumberNull() {
		$train = new Train(null, null, null, null, null);
		
		$this->assertIsString($train->getRunNumber());
	}
	
	/**
	 * @covers \Trains\Model\Train::getId
	 * @return void
	 */
	public function testGetId() {
		$id = random_int(0, PHP_INT_MAX);
		$line = StringUtil::randomString(45);
		$route = StringUtil::randomString(45);
		$runNumber = StringUtil::randomString(45);
		$operatorId = StringUtil::randomString(45);
		$train = new Train($line, $route, $runNumber, $operatorId, $id);
		
		$this->assertIsInt($train->getId());
		$this->assertEquals($id, $train->getId());
	}
	
	/**
	 * @covers \Trains\Model\Train::getId
	 * @return void
	 */
	public function testGetIdNull() {
		$train = new Train(null, null, null, null, null);
		
		$this->assertNull($train->getId());
	}
	
	/**
	 * @covers \Trains\Model\Train::getOperatorId
	 * @return void
	 */
	public function testGetOperatorId() {
		$id = random_int(0, PHP_INT_MAX);
		$line = StringUtil::randomString(45);
		$route = StringUtil::randomString(45);
		$runNumber = StringUtil::randomString(45);
		$operatorId = StringUtil::randomString(45);
		$train = new Train($line, $route, $runNumber, $operatorId, $id);
		
		$this->assertIsString($train->getOperatorId());
		$this->assertStringContainsString($operatorId, $train->getOperatorId());
	}
	
	/**
	 * @covers \Trains\Model\Train::getOperatorId
	 * @return void
	 */
	public function testGetOperatorIdNull() {
		$train = new Train(null, null, null, null, null);
		
		$this->assertIsString($train->getOperatorId());
	}
}
