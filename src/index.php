<?php

use Trains\Controller\TrainsController;
use Http\HttpRequest;

require_once "../vendor/autoload.php";
require_once "ComponentSystem/constants.conf.php";

((new TrainsController(HttpRequest::current()))->handleRequest())->send();
