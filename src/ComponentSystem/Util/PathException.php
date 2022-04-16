<?php

namespace Util;

use Exception;

/**
 * An exception related to a Path
 * @author James Polk
 */
class PathException extends Exception {
	
	public function __construct(string $message) {
		parent::__construct($message);
	}
	
}