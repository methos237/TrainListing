<?php

namespace Trains\Test\Model;

use PHPUnit\Framework\TestCase;
use Trains\Model\Train;
use Trains\Model\TrainsCSVParser;

class TrainsCsvParserTest extends TestCase {
	
	/**
	 * @covers \Trains\Model\TrainsCSVParser::parseCsvFile
	 * @return void
	 */
    public function testParseCsvFile (): void {
	    $csvFile = __DIR__ . "/data/trains_6.csv";
        $trains = new TrainsCSVParser($csvFile);
		$csvArray = $trains->parseCsvFile ($csvFile);
		$this->assertIsArray($csvArray);
		$this->assertNotEmpty($csvArray);
    }
	
	/**
	 * @covers \Trains\Model\TrainsCSVParser::getTrainDataArray
	 * @return void
	 */
	public function testGetTrainDataArray() {
		$csvFile = __DIR__ . "/data/trains_6.csv";
		$trains = new TrainsCSVParser($csvFile);
		$this->assertIsArray($trains->getTrainDataArray());
		$this->assertNotEmpty($trains->getTrainDataArray());
		$this->assertIsObject($trains->getTrainDataArray()[0]);
		$this->assertInstanceOf(Train::class, $trains->getTrainDataArray()[0]);
	}
}
