<?php

namespace Http;

/**
 * A general purpose HTTP client
 * @author James Polk
 */
class HttpClient {
	
	private const DEFAULT_TIMEOUT = 10000;
	
	protected int $timeout;
	protected $log;
	private array $curlOptions;
	
	/**
	 * Create a new HTTP client
	 * @param int $timeout - The request timeout(in milliseconds)
	 * @param array $additionalCurlOptions - Array of curl options to add or override defaults
	 */
	public function __construct(int $timeout = self::DEFAULT_TIMEOUT, array $additionalCurlOptions = []) {
		$this->timeout = $timeout;
		$this->log = $this->createLogStream();
		$this->curlOptions = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT_MS => $this->timeout,
			CURLOPT_VERBOSE => true
		];
		foreach ($additionalCurlOptions as $optInteger => $value) {
			$this->curlOptions[$optInteger] = $value;
		}
	}
	
	/**
	 * Create a new in-memory stream for cURL to use for logging
	 * @return null|resource - A handle to the in-memory stream or null if a stream cannot be created
	 */
	protected function createLogStream() {
		$stream = fopen("php://memory", "w+");
		if ($stream === false) {
			return null;
		}
		return $stream;
	}
	
	/**
	 * Get the base cURL options based on the settings for this HTTP client
	 * @return array - An associative
	 */
	protected function getCurlOptions(): array {
		return $this->curlOptions;
	}
	
	/**
	 * Format an associative array of headers into an indexed array for cURL
	 * @param array $headers - An associative array where the key is the header field and the value is the header value
	 * @return array - An indexed array of strings in the format "Header-Field: Header-Value"
	 */
	protected function formatHeaders(array $headers): array {
		$formatted = [];
		foreach ($headers as $field => $value) {
			$formatted[] = $field . HttpUtil::HEADER_DELIMITER . " " . $value;
		}
		return $formatted;
	}
	
	/**
	 * Parse a header into a key and value
	 * @param string $raw - The raw header
	 * @param array $headers - An associative array to which to add the parsed header
	 * @return int - The number of bytes read
	 */
	protected function parseHeader(string $raw, array &$headers): int {
		$split = explode(HttpUtil::HEADER_DELIMITER, $raw, 2);
		if (count($split) === 2) {
			$headers[$split[0]] = trim(str_replace(["\n", "\r"], "", $split[1]));
		}
		return strlen($raw);
	}
	
	/**
	 * Send an HTTP request
	 * @param HttpRequest $request - The request to send
	 * @return HttpResponse - The response to the provided request
	 */
	public function send(HttpRequest $request): HttpResponse {
		//Initialize a cURL handle for the request URL
		$curl = curl_init((string)$request->getUrl());
		if ($curl === false) {
			throw new CurlHttpException("Unable to initialize cURL", $curl);
		}
		//Set the options for cURL based on this client's settings
		if (!curl_setopt_array($curl, $this->getCurlOptions())) {
			throw new CurlHttpException("Failed to set cURL options", $curl);
		}
		//Point stderr for cURL to an in-memory stream for logging
		if (($this->log !== null) && !curl_setopt($curl, CURLOPT_STDERR, $this->log)) {
			throw new CurlHttpException("Failed to set stderr for cURL");
		}
		//Set the request method
		if (!curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod())) {
			throw new CurlHttpException("Failed to set request method", $curl);
		}
		//Set the request headers
		if (!curl_setopt($curl, CURLOPT_HTTPHEADER, $this->formatHeaders($request->getHeaders()))) {
			throw new CurlHttpException("Failed to set request headers", $curl);
		}
		//Set the request body
		if (!curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getBody())) {
			throw new CurlHttpException("Failed to set request body", $curl);
		}
		//Set the callback for the response headers
		$responseHeaders = [];
		if (!curl_setopt($curl, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$responseHeaders) {
			return $this->parseHeader($header, $responseHeaders);
		})) {
			throw new CurlHttpException("Failed to set header callback for cURL", $curl);
		}
		//Execute the request
		$responseBody = curl_exec($curl);
		if ($responseBody === false) {
			throw new CurlHttpException("cURL request failed", $curl);
		}
		$responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		//Destroy the curl resource
		curl_close($curl);
		return new HttpResponse(
			$responseCode,
			$responseBody,
			$responseHeaders
		);
	}
	
	/**
	 * Get the request log for this client as a string
	 * @return string - The log content
	 */
	public function getLog(): string {
		if (!rewind($this->log)) {
			throw new HttpException("Unable to rewind cURL log stream");
		}
		$log = stream_get_contents($this->log);
		if ($log === false) {
			throw new HttpException("Unable to read cURL log");
		}
		return $log;
	}
	
}