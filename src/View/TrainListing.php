<?php

namespace Trains\View;

use ComponentSystem\Component;
use Trains\Model\TrainsModel;

/**
 * The component displays the train listing to the user.
 */
class TrainListing extends Component {
	
	private ?TrainsModel $model;
	
	/**
     * Create a new TrainListing
	 * @param TrainsModel|null $model
	 */
	public function __construct(?TrainsModel $model = null) {
		$this->model = $model;
	}
	
	
	/**
	 * @inheritDoc
	 */
	public function display(): void {
		if ($this->model === null): ?>
            <h2>Please Upload a CSV File</h2>
		<?php endif; ?>
		<?php
	}
}