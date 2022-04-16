<?php

namespace Http;

/**
 * A generic representation of the response to an HTTP request
 * TODO: Address casing of header fields
 *
 * @author James Polk
 */
class HttpResponse {
	
	protected $responseCode;
	protected array $responseHeaders;
	protected ?string $responseBody;
	protected ?string $responseMessage;
	
	/**
	 * Create a new HTTPResponse
	 *
	 * @param int|CustomHttpResponseCode $responseCode - The HTTP response code or 0 if request failed entirely
	 *     (Default: 200)
	 * @param string|null $responseBody - The response body (Default: null)
	 * @param array $responseHeaders - An associative array of response headers[field=>value] (Default: [])
	 */
	public function __construct($responseCode = 200, ?string $responseBody = null, array $responseHeaders = [], string $responseMessage = null) {
		//TODO: Find a cleaner and more consistent way to handle the response code and message
		if ($responseCode instanceof CustomHttpResponseCode) {
			$this->responseCode = $responseCode->getCode();
			$this->responseMessage = $responseCode->getMessage();
		} else {
			$this->responseCode = $responseCode;
			$this->responseMessage = $responseMessage;
		}
		$this->responseBody = (string)$responseBody;
		$this->responseHeaders = $responseHeaders;
	}
	
	public function getResponseCode() {
		return $this->responseCode;
	}
	
	public function getResponseMessage(): ?string {
		return $this->responseMessage;
	}
	
	public function setResponseHeader(string $field, string $value): void {
		$this->responseHeaders[$field] = $value;
	}
	
	public function getResponseHeaders(): array {
		return $this->responseHeaders;
	}
	
	/**
	 * Get the value of a header field
	 *
	 * @param string $field - The name of the header field
	 * @return string - The value
	 */
	public function getResponseHeader(string $field): ?string {
		return $this->responseHeaders[$field] ?? null;
	}
	
	public function hasResponseHeader(string $field): bool {
		return array_key_exists($field, $this->responseHeaders);
	}
	
	public function getResponseBody(): ?string {
		return $this->responseBody;
	}
	
	/**
	 * Send the response and exit
	 *
	 * This sets the response code and headers from this response,
	 * outputs the response body, and then exits.
	 */
	public function send(): void {
		HttpUtil::setResponseCode($this->getResponseCode(), $this->getResponseMessage());
		foreach ($this->getResponseHeaders() as $field => $value) {
			HttpUtil::setResponseHeader($field, $value);
		}
		echo $this->getResponseBody();
		exit;
	}
}