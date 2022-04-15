<?php

namespace Trains\Test;

use PHPUnit\Framework\TestCase;
use Trains\Model\TrainsModel;

class TrainsModelTest extends TestCase {
	
	/**
	 * @covers \Trains\Model\TrainsModel::parseCsvFile
	 * @return void
	 */
    public function testParseCsvFile (): void {
	    $csvFile = "tests/data/trains_6.csv";
        $trains = new TrainsModel($csvFile);
		$csvArray = $trains->parseCsvFile ($csvFile);
		$this->assertIsArray($csvArray);
		$this->assertNotEmpty($csvArray);
    }
	
	/**
	 * @covers \Trains\Model\TrainsModel::getNumberOfTrains
	 * @return void
	 */
	public function testGetNumberOfTrains(): void {
		$csvFile = "tests/data/trains_6.csv";
		$trains = new TrainsModel($csvFile);
		$this->assertSame(6, $trains->getNumberOfTrains());
	}
}
