<?php

namespace Trains\Model;

class Train {
	
	private string $line;
	private string $route;
	private string $runNumber;
	private string $operatorId;
	
	
	/**
	 * @param string $line
	 * @param string $route
	 * @param string $runNumber
	 * @param string $operatorId
	 */
	public function __construct(string $line, string $route, string $runNumber, string $operatorId) {
		$this->line = $line;
		$this->route = $route;
		$this->runNumber = $runNumber;
		$this->operatorId = $operatorId;
	}
	
	
	/**
	 * @return string
	 */
	public function getLine(): ?string {
		return !empty($this->line) ? $this->line : null;
	}
	
	
	/**
	 * @return string|null
	 */
	public function getRoute(): ?string {
		return !empty($this->route) ? $this->route : null;
	}
	
	
	/**
	 * @return string
	 */
	public function getRunNumber(): ?string {
		return !empty($this->runNumber) ? $this->runNumber : null;
	}
	
	
	/**
	 * @return string
	 */
	public function getOperatorId(): ?string {
		return !empty($this->operatorId) ? $this->operatorId : null;
	}
	
	
}