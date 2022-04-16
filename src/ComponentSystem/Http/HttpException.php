<?php

namespace Http;

use Throwable;
use RuntimeException;

/**
 * An Exception related to an HTTP request
 */
class HttpException extends RuntimeException {
	
	public function __construct(string $message, Throwable $cause = null) {
		parent::__construct($message, 0, $cause);
	}
	
}