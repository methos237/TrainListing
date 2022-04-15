<?php
require_once ("../vendor/autoload.php");

use ComponentSystem\Page;
use Trains\View\Footer;
use Trains\View\Header;
use Trains\View\TrainListing;

echo (new Page("Train Listing", "Lists train data from an uploaded CSV file", null, null, new TrainListing()))
		->setHeader(new Header())
		->setFooter(new Footer());