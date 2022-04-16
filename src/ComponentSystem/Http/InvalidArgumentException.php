<?php

namespace Http;

use RuntimeException;

/**
 * Exception for invalid arguments
 * @author James Polk
 */
class InvalidArgumentException extends RuntimeException {
	public function __construct(string $message) {
		parent::__construct($message);
	}
}