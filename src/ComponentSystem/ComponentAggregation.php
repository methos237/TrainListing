<?php

namespace ComponentSystem;

/**
 * Combines the dependencies and lifetimes for all components in the container
 */
trait ComponentAggregation {
	
	/**
	 * Get the Components that make up the container
	 * @return array|Component[]
	 */
	abstract public function getComponents(): array;
	
	/**
	 * Merge all component dependencies into one array
	 * @return array - All component dependencies
	 */
	public function getDependencies(): array {
		$dependencies = [parent::getDependencies()];
		foreach ($this->getComponents() as $component) {
			$dependencies[] = $component->getDependencies();
		}
		return array_merge(...array_values($dependencies));
	}
	
	/**
	 * Get the minimum TTL (for caching purposes) of all component lifetimes.
	 * @return int - TTL in seconds
	 */
	public function getLifetime(): int {
		$components = $this->getComponents();
		return (empty($components)) ? parent::getLifetime() : min(array_map(static function($component) { return $component->getLifetime(); }, $components));
	}
	
}