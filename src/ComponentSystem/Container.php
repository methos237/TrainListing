<?php

namespace ComponentSystem;

/**
 * Creates a container that is made up of one or more Components
 */
abstract class Container extends Component {
	use ComponentAggregation;
	
	/**
	 * Present this container as a sequence of its subcomponents
	 */
	public function display(): void {
		foreach ($this->getComponents() as $component) {
			$component->display();
		}
	}
}