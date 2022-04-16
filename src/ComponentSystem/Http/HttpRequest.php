<?php

namespace Http;

/**
 * A generic representation of an HTTP request
 * TODO: Better define handling of query string parameters(params vs url)
 * @author James Polk
 */
class HttpRequest {
	private const SENSITIVE_FIELDS = ['password', 'pass'];
	
	private static $current = null;
	
	protected Url $url;
	protected string $method;
	protected array $headers;
	protected ?string $body;
	protected array $params;
	protected array $cookies;
	protected ?string $clientAddress;
	protected ?string $username;
	protected ?string $password;
	
	/**
	 * Create a new HttpRequest
	 * @param Url $url - The requested URL
	 * @param string $method - The request method [Default: GET]
	 * @param array $headers - An associative array of the request headers [Default: []]
	 * @param string|null $body - The request body [Default: null]
	 * @param array $params - The request parameters [Default: []]
	 * @param array $cookies - The request cookies
	 * @param string|null $clientAddress - The client IP address
	 * @param string|null $username - The user name passed in the request authorization(Basic)
	 * @param string|null $password - The password passed in the request authorization header(Basic)
	 */
	public function __construct(Url $url, string $method = REQUEST_METHOD_GET, array $headers = [], string $body = null, array $params = [], array $cookies = [], string $clientAddress = null, string $username = null, string $password = null) {
		$this->url = $url;
		$this->method = $method;
		$this->headers = array_change_key_case($headers, CASE_UPPER);
		$this->body = $body;
		$this->params = $params;
		$this->cookies = $cookies;
		$this->clientAddress = $clientAddress;
		$this->username = $username;
		$this->password = $password;
	}
	
	public function getUrl(): Url {
		return $this->url;
	}
	
	public function getMethod(): string {
		return $this->method;
	}
	
	public function getHeaders(): array {
		return $this->headers;
	}
	
	/**
	 * Get a request header by name
	 * @return string|null - The header value or null of the header does not exist
	 */
	public function getHeader(string $key): ?string {
		return $this->headers[strtoupper($key)] ?? null;
	}
	
	public function getBody(): string {
		return (string)$this->body;
	}
	
	public function getParams(): array {
		return $this->params;
	}
	
	/**
	 * Check if the request includes the specified parameter
	 * @param string $name - The parameter name
	 */
	public function hasParam(string $name): bool {
		return array_key_exists($name, $this->params);
	}
	
	/**
	 * Get a request parameter by name
	 * @param string $name - The parameter name
	 * @return string|null - The parameter value or null if the parameter has no value
	 * TODO: PHP parses certain parameters as arrays, so this can actually return an array; return type intentionally removed to resolve this for now.
	 */
	public function getParam(string $name) {
		return $this->params[$name] ?? null;
	}
	
	public function getCookies(): array {
		return $this->cookies;
	}
	
	/**
	 * Check if the request includes the specified cookie
	 * @param string $name - The name of the cookie to check
	 * @return bool - True if this request includes the cookie, false otherwise
	 */
	public function hasCookie(string $name): bool {
		return array_key_exists($name, $this->cookies);
	}
	
	/**
	 * Get a specific cookie
	 * @param string $name - The name of the cookie
	 * @return null|string - The value or null if the cookie does not exist
	 */
	public function getCookie(string $name): ?string {
		return $this->cookies[$name] ?? null;
	}
	
	public function getClientAddress(): ?string {
		return $this->clientAddress;
	}
	
	public function getUsername(): ?string {
		return $this->username;
	}
	
	public function getPassword(): ?string {
		return $this->password;
	}
	
	/**
	 * Redact various places where credentials may be present in an HTTP Request and
	 *   return the result for logging purposes
	 * @return HttpRequest
	 */
	public function redactCredentials(): HttpRequest {
		$request = clone $this;
		$request->body = $this->scanForPasswords($this->getBody());
		$request->params = $this->scanForPasswords($this->getParams());
		if (isset($request->password)) {
			$request->password = '[REDACTED]';
		}
		if (isset($request->headers['AUTHORIZATION'])) {
			$request->headers['AUTHORIZATION'] = explode(' ', $request->headers['AUTHORIZATION'])[0] . ' [REDACTED]';
		}
		if (isset($request->headers['PROXY-AUTHORIZATION'])) {
			$request->headers['PROXY-AUTHORIZATION'] = explode(' ', $request->headers['PROXY_AUTHORIZATION'])[0] . ' [REDACTED]';
		}
		return $request;
	}
	
	private function scanForPasswords($value) {
		foreach (self::SENSITIVE_FIELDS as $keyword) {
			if (is_object($value)) {
				if (isset($value->$keyword)) {
					$value->$keyword = '[REDACTED]';
				}
			} else if (is_array($value)) {
				if (isset($value[$keyword])) {
					$value[$keyword] = '[REDACTED]';
				}
			} else if (is_string($value)) {
				// Searches string for keyword with or without quotes
				//   followed by a colon or equal sign with or without spacing
				//   and redacts anything thereafter
				$output = preg_replace("/([\"']?{$keyword}[\"']?\h*[:=]\h*).*/", "$1[REDACTED]", $value);
				if ($output !== $value) {
					$value = $output;
				}
			}
		}
		return $value;
	}
	
	/**
	 * Get the current HttpRequest
	 * @return HttpRequest - The current request
	 */
	public static function current(): HttpRequest {
		if (self::$current === null) {
			$method = HttpUtil::getRequestMethod();
			$headers = array_change_key_case(getallheaders(), CASE_UPPER);
			$contentType = $headers["CONTENT-TYPE"] ?? null;
			$body = file_get_contents("php://input");
			$params = [];
			if ($contentType === HttpUtil::CONTENT_TYPE_JSON) {
				$params = json_decode($body, true) ?? [];
			} else {
				switch ($method) {
					case REQUEST_METHOD_GET:
						$params = $_GET;
						break;
					case REQUEST_METHOD_POST:
						$params = $_POST;
						break;
					case REQUEST_METHOD_PUT:
					case REQUEST_METHOD_DELETE:
						if (!empty($body)) {
							parse_str($body, $params);
						} else {
							$params = $_REQUEST;
						}
						break;
				}
			}
			self::$current = new HttpRequest(
				Url::fromRequest(),
				$method,
				$headers,
				$body,
				$params,
				$_COOKIE,
				$_SERVER["REMOTE_ADDR"] ?? null,
				$_SERVER["PHP_AUTH_USER"] ?? null,
				$_SERVER["PHP_AUTH_PW"] ?? null
			);
		}
		return self::$current;
	}
	
}