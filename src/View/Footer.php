<?php

namespace Trains\View;


use ComponentSystem\Component;

class Footer extends Component {
	
	public function display(): void { ?>
        <footer>
            <div class="container" style="text-align: center">
                <p>&copy; 2022 James Knox Polk. For Consideration Only.</p>
            </div>
        </footer>
	<?php
	}
}