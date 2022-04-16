<?php

namespace Trains\View;

use ComponentSystem\Component;
use Trains\Model\Train;

/**
 * The component displays the train listing to the user.
 */
class TrainListing extends Component {
	
	private ?array $trains;
    private ?string $statusMessage;
	
	/**
	 * Create a new TrainListing
	 * @param array<Train>|null $trains
     * @param string|null $statusMessage - message to display based on form interaction
	 */
	public function __construct(?array $trains = null, ?string $statusMessage = null) {
		$this->trains = $trains;
        $this->statusMessage = $statusMessage;
	}
	
	
	/**
	 * @inheritDoc
	 */
	public function display(): void { ?>
        <?php if (empty($this->trains)): ?>
            <div class="row">
                <div class="column column" style="text-align: center">
                    <p>There is currently no train information to display. Please Upload a CSV File above, or manually enter the information below.</p>
                </div>
            </div>
            <div class="row">
                <div class="column column" style="text-align: center">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Train Line</th>
                            <th>Route</th>
                            <th>Run Number</th>
                            <th>Operator ID</th>
                        </tr>
                        </thead>
                        <form action="" method="post" enctype="multipart/form-data">
                            <tr>
                                <td><input type="text" name="line" placeholder="Train Line"/></td>
                                <td><input type="text" name="route" placeholder="Route"/></td>
                                <td><input type="text" name="run_number" placeholder="Run Number"/></td>
                                <td><input type="text" name="operator_id" placeholder="Operator ID"/></td>
                                <td><button type="submit" name="submit" value="add">Add Train</button></td>
                            </tr>
                        </form>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <?php if ($this->statusMessage !== null): ?>
                <div class="row">
                    <div class="column column" style="text-align: center">
                        <p><?= $this->statusMessage ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
            <div class="column column" style="text-align: center">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Train Line</th>
                            <th>Route</th>
                            <th>Run Number</th>
                            <th>Operator ID</th>
                        </tr>
                    </thead>
		            <?php foreach ($this->trains as $train): ?>
                        <form action="" method="post" enctype="multipart/form-data">
                            <tr>
                                <input type="hidden" name="id" value="<?= html_entity_decode($train->getId()) ?>"/>
                                <td><input type="text" name="line" value="<?= html_entity_decode($train->getLine()) ?>"/></td>
                                <td><input type="text" name="route" value="<?= html_entity_decode($train->getRoute()) ?>"/></td>
                                <td><input type="text" name="run_number" value="<?= html_entity_decode($train->getRunNumber()) ?>"/></td>
                                <td><input type="text" name="operator_id" value="<?= html_entity_decode($train->getOperatorId()) ?>"/></td>
                                <td><button type="submit" name="submit" value="edit">Edit</button></td>
                                <td><button type="submit" name="submit" value="delete">Delete</button></td>
                            </tr>
                        </form>
		            <?php endforeach; ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <tr>
                            <input type="hidden" name="id" value=""/>
                            <td><input type="text" name="line" placeholder="Train Line"/></td>
                            <td><input type="text" name="route" placeholder="Route"/></td>
                            <td><input type="text" name="run_number" placeholder="Run Number"/></td>
                            <td><input type="text" name="operator_id" placeholder="Operator ID"/></td>
                            <td><button type="submit" name="submit" value="add">Add Train</button></td>
                        </tr>
                    </form>
                </table>
            </div>
            </div>
        <?php endif; ?>
    <?php
	}
}