<?php

namespace Http;

interface ContentType {
	
	/**
	 * Get the MIME type that identifies this content type
	 * @return MimeType
	 */
	public function getMimeType(): MimeType;
	
	/**
	 * Decode data formatted in this content type
	 * @param string $data - The encoded data to decode
	 * @return mixed - The decoded data
	 */
	public function decode(string $data);
	
	/**
	 * Encode data using this content type
	 * @param mixed $data - The data to encode
	 * @return string - The encoded data
	 */
	public function encode($data): string;
	
	/**
	 * Represent this content type as a string usable as in the Content-Type header
	 */
	public function __toString(): string;
	
}