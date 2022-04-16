<?php

namespace Http;

class CustomHttpResponseCode {
	
	protected int $code;
	protected string $message;
	
	public function __construct(int $code, string $message) {
		$this->code = $code;
		$this->message = $message;
	}
	
	public function getCode(): int {
		return $this->code;
	}
	
	public function getMessage(): string {
		return $this->message;
	}
	
}