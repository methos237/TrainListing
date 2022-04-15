<?php

namespace Trains\Model;

use RuntimeException;

class TrainsModel {
	
	private const LINE_HEADER = "TRAIN_LINE";
	private const ROUTE_HEADER = "ROUTE_NAME";
	private const RUN_HEADER = "RUN_NUMBER";
	private const OPERATOR_HEADER = "OPERATOR_ID";

    private array $trains = [];
	
	/**
	 * @param string $trainData
	 */
    public function __construct ( string $trainData ) {
        if (!file_exists ($trainData)) {
	        throw new RuntimeException("CSV File $trainData not found.");
        }
		$dataArray = $this->parseCsvFile($trainData);
		foreach ($dataArray as $data) {
			$this->trains[] = new Train($data[self::LINE_HEADER], $data[self::ROUTE_HEADER], $data[self::RUN_HEADER], $data[self::OPERATOR_HEADER]);
		}
    }
	
	public function getTrains(): array {
		return $this->trains;
	}
	
	public function getNumberOfTrains(): int {
		return count($this->trains);
	}
	
	/**
	 * Parses a CSV file into a trimmed associative array
	 * @param string $csvFile - Path of the CSV file
	 * @return array - Associative array of trimmed CSV data including headers.
	 */
    public function parseCsvFile(string $csvFile): array {
	    if (!file_exists ($csvFile)) {
		    throw new RuntimeException("CSV File $csvFile not found.");
	    }
	    $rows = array_map('str_getcsv', file($csvFile, FILE_SKIP_EMPTY_LINES));
	    $header = array_map('trim',array_shift($rows));
	    $output = [];
	    foreach ($rows as $row) {
		    $output[] = array_combine($header, array_map('trim', $row));
	    }
	    return $output;
    }
	


}