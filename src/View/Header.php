<?php

namespace Trains\View;


use ComponentSystem\Component;

class Header extends Component {
	
	public function display(): void { ?>
        <header>
            <h1>Welcome to the Train Listing Parser</h1>
        </header>
	<?php
	}
}