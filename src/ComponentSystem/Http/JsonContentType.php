<?php

namespace Http;

class JsonContentType implements ContentType {
	
	public function getMimeType(): MimeType {
		return MimeType::JSON();
	}
	
	public function decode(string $data) {
		return json_decode($data, true);
	}
	
	public function encode($data): string {
		return json_encode($data);
	}
	
	public function __toString(): string {
		return (string)$this->getMimeType();
	}
	
}