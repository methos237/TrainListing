<?php

namespace Trains\View;


use ComponentSystem\Component;

class Header extends Component {
	
	public function display(): void { ?>
        <header>
            <div class="container" style="text-align: center">
                <a href="/"><h1>Welcome to the Train Listing Parser</h1></a>
            </div>
        </header>
	<?php
	}
}