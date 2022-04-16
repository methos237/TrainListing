<?php

namespace Trains\Model;

/**
 * This class represents a single train
 */
class Train {
	
	private ?string $line;
	private ?string $route;
	private ?string $runNumber;
	private ?string $operatorId;
	private ?int $id;
	
	
	/**
	 * @param string|null $line
	 * @param string|null $route
	 * @param string|null $runNumber
	 * @param string|null $operatorId
	 * @param int|null $id
	 */
	public function __construct(?string $line, ?string $route, ?string $runNumber, ?string $operatorId, ?int $id = null) {
		$this->line = $line;
		$this->route = $route;
		$this->runNumber = $runNumber;
		$this->operatorId = $operatorId;
		$this->id = $id;
	}
	
	
	/**
	 * @return string
	 */
	public function getLine(): string {
		return $this->line ?? "";
	}
	
	
	/**
	 * @return string
	 */
	public function getRoute(): string {
		return $this->route ?? "";
	}
	
	
	/**
	 * @return string
	 */
	public function getRunNumber(): string {
		return $this->runNumber ?? "";
	}
	
	
	/**
	 * @return string
	 */
	public function getOperatorId(): string {
		return $this->operatorId ?? "";
	}
	
	/**
	 * @return int|null
	 */
	public function getId(): ?int {
		return $this->id;
	}
	
	/**
	 * Creates a Train object from an array of parameters
	 * @param array $array
	 * @return Train
	 */
	public static function fromArray(array $array): Train {
		return new self($array['line'], $array['route'], $array['run_number'], $array['operator_id'], $array['id']);
	}
}