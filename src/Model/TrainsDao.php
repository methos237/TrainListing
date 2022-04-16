<?php

namespace Trains\Model;

use ComponentSystem\Util\Database\DatabaseManager;
use PDO;

/**
 * This class provides CRUD operations to the 'trains' database table
 */
class TrainsDao {
	
	private $connection;
	
	public function __construct() {
		$this->connection = DatabaseManager::connect();
	}
	
	/**
	 * Fetch a single row from the table by ID
	 * TODO: This is not yet implemented, but may be useful in future updates
	 * @param int $id - the unique ID to fetch
	 * @return Train|null - Train object or null on failure
	 */
	public function getTrainInformationById(int $id): ?Train {
		$sql = $this->connection->prepare("SELECT * FROM trains WHERE id = ?");
		$sql->execute([$id]);
		$row = $sql->fetchAll(PDO::FETCH_ASSOC);
		if (!empty($row)) {
			return Train::fromArray($row[0]);
		}
		return null;
	}
	
	/**
	 * Fetch all trains in the table
	 * @return array|null - returns an array of Train objects, or null on an empty table
	 */
	public function getAllTrainInformation(): ?array {
		$rows = $this->connection->query("SELECT * FROM trains ORDER BY run_number ASC")
			->fetchAll(PDO::FETCH_ASSOC);
		if (!empty($rows)) {
			$trains = [];
			foreach ($rows as $row) {
				$trains[] = Train::fromArray($row);
			}
			return $trains;
		}
		return null;
		
	}
	
	/**
	 * Store a single train in the table
	 * @param Train $train - the train to store
	 * @return bool - false on error
	 */
	public function storeSingleTrainInformation(Train $train): bool {
		$sql = $this->connection->prepare("INSERT INTO trains (line, route, run_number, operator_id) VALUES (:line, :route, :run_number, :operator_id)");
		if (!$sql->execute([
			'line' => $train->getLine(),
			'route' => $train->getRoute(),
			'run_number' => $train->getRunNumber(),
			'operator_id' => $train->getOperatorId()
		])) {
			return false;
		}
		return true;
	}
	
	/**
	 * Store multiple trains in the table at once
	 * Duplicate entries will not be written
	 * @param array<Train> $trains - an array of Train objects
	 * @return bool - false on error
	 */
	public function storeMultipleTrainInformation(array $trains): bool {
		$sql = $this->connection->prepare("INSERT INTO trains (line, route, run_number, operator_id) VALUES (:line, :route, :run_number, :operator_id)");
		$trainCount = count($trains);
		foreach ($trains as $train) {
			if (!$sql->execute([
				'line' => $train->getLine(),
				'route' => $train->getRoute(),
				'run_number' => $train->getRunNumber(),
				'operator_id' => $train->getOperatorId()
			])) {
				// if an insert fails (most likely a duplicate), decrement the count
				--$trainCount;
			}
		}
		// if the count is > 0, it means new rows were added. Score!
		return ($trainCount > 0);
	}
	
	/**
	 * Update a single row in the table
	 * @param Train $updatedTrain - the updated Train object
	 * @return bool - false on error
	 */
	public function updateTrainInformation(Train $updatedTrain): bool {
		$sql = $this->connection->prepare("UPDATE trains SET line=:line, route=:route, run_number=:run_number, operator_id=:operator_id WHERE id=:id");
		if (!$sql->execute([
			'id' => $updatedTrain->getId(),
			'line' => $updatedTrain->getLine(),
			'route' => $updatedTrain->getRoute(),
			'run_number' => $updatedTrain->getRunNumber(),
			'operator_id' => $updatedTrain->getOperatorId()
		]))  {
			return false;
		}
		return true;
	}
	
	/**
	 * Deletes a single row from the table
	 * @param Train $train - Train object to delete
	 * @return bool - false on error
	 */
	public function deleteTrainInformation(Train $train): bool {
		$id = $train->getId();
		$sql = $this->connection->prepare("DELETE FROM trains WHERE id=:id");
		if (!$sql->execute(['id' => $id])) {
			return false;
		}
		return true;
	}
}