<?php

namespace Http;

/**
 * A generic HTTP response in which the body is JSON
 * @author James Polk
 */
class JsonHttpResponse extends HttpResponse {
	
	protected $responseData;
	
	/**
	 * Create a new JsonHttpResponse
	 * @param int $responseCode - The HTTP response code or 0 if the request failed entirely (Default: 200)
	 * @param mixed $responseData - An array representing the response data or a JSON string (Default: [])
	 * @param array $responseHeaders - An associative array of response headers[field=>value] (Default: [])
	 */
	public function __construct(int $responseCode = 200, $responseData = null, array $responseHeaders = []) {
		if (is_string($responseData)) {
			$responseData = json_decode($responseData);
			if ($responseData === null) {
				throw new HttpException("Unable to parse JSON response data: " . $data);
			}
		}
		$responseHeaders["Content-Type"] = "application/json";
		parent::__construct($responseCode, json_encode($responseData), $responseHeaders);
		$this->responseData = $responseData;
	}
	
	/**
	 * Get the response data
	 * @return array - An associative array representing the response JSON
	 */
	public function getResponseData(): array {
		return $this->responseData;
	}
	
}