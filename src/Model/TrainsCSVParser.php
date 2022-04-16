<?php

namespace Trains\Model;

use RuntimeException;

/**
 * This class takes a CSV file with headers and converts it to an
 * array of Train Objects
 */
class TrainsCSVParser {
	
	private array $trainDataArray;
	
	/**
	 * Convert a new CSV file to an array of Train objects
	 * @param string $trainData - the path to the CSV file
	 */
    public function __construct ( string $trainData ) {
        if (!file_exists ($trainData)) {
	        throw new RuntimeException("CSV File $trainData not found.");
        }
		$parsedData = $this->parseCsvFile($trainData);
		foreach ($parsedData as $data) {
			$this->trainDataArray[] = new Train($data['TRAIN_LINE'], $data['ROUTE_NAME'], $data['RUN_NUMBER'], $data['OPERATOR_ID']);
		}
    }
	
	/**
	 * @return array - an array of Train objects
	 */
	public function getTrainDataArray(): array {
		return $this->trainDataArray;
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