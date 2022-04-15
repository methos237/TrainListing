<?php

namespace Trains\View;


use ComponentSystem\Component;

class Footer extends Component {
	
	public function display(): void { ?>
        <footer>
            <p>&copy; 2022 James Knox Polk. For Consideration Only.</p>
        </footer>
	<?php
	}
}