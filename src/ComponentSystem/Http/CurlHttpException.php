<?php

namespace Http;

/**
 * An HTTP Exception caused by an underlying cURL error
 * @author James Polk
 */
class CurlHttpException extends HttpException {
	
	/**
	 * @param $curlHandle - The cURL handle on which the error occurred
	 */
	public function __construct(string $message, $curlHandle) {
		parent::__construct($message . ": " . $this->getLastError($curlHandle));
	}
	
	private function getLastError($curlHandle): string {
		$errorNumber = curl_errno($curlHandle);
		return curl_error($curlHandle) . "($errorNumber) - " . curl_strerror($errorNumber);
	}
	
}