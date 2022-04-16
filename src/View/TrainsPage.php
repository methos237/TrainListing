<?php

namespace Trains\View;

use ComponentSystem\Component;
use ComponentSystem\Container;

class TrainsPage extends Container {
	
	private Component $uploadForm;
	private Component $trainListing;
	
	/**
	 * @param Component $uploadForm
	 * @param Component $trainListing
	 */
	public function __construct(Component $uploadForm, Component $trainListing) {
		$this->uploadForm = $uploadForm;
		$this->trainListing = $trainListing;
	}
 
	/**
	 * @inheritDoc
	 */
	public function getComponents(): array {
		return [$this->uploadForm, $this->trainListing];
	}
	
	/**
	 * @inheritDoc
	 */
	public function display(): void { ?>
		<div class = "container">
			<?php parent::display() ?>
		</div>
	<?php
	}
}