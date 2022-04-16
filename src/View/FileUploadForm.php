<?php

namespace Trains\View;

use ComponentSystem\Component;

class FileUploadForm extends Component {
    
    private ?string $errorMessage;
	
	/**
     * Create a new Upload Form
	 * @param string|null $errorMessage - Any error message passed from the controller during file upload
	 */
	public function __construct(?string $errorMessage = null) {
		$this->errorMessage = $errorMessage;
	}
	
	
	/**
	 * @inheritDoc
	 */
	public function display(): void { ?>
        <div class="row">
            <div class="column column" style="text-align: center">
                <p>Please select a CSV file to add trains to the listing</p>
            </div>
        </div>
		<?php if ($this->errorMessage !== null): ?>
            <div class="row">
                <div class="column column" style="text-align: center">
                    <p style="color: darkred"><?= $this->errorMessage ?></p>
                </div>
            </div>
		<?php endif; ?>
        <div class="row">
            <div class="column column" style="text-align: center">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="file" name="train_csv" value="" />
                    <button type="submit" name="submit" value="upload">Upload</button></form>
            </div>
        </div>
	<?php
	}
}