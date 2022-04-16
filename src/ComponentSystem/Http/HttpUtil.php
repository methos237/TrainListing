<?php

namespace Http;

use Util\StringUtil;
use Util\ArrayUtil;

/**
 * Contains various utility methods related to HTTP
 * @author James Polk
 */
class HttpUtil {
	
	private function __construct() {
		//Prevent instantiation of this class as it only contains static utility methods
	}
	
	private const MIN_RESPONSE_CODE = 100;
	private const MAX_RESPONSE_CODE = 599;
	
	public const RESPONSE_SERIES_UNRECOGNIZED = 0;
	public const RESPONSE_SERIES_INFORMATIONAL = 100;
	public const RESPONSE_SERIES_SUCCESSFUL = 200;
	public const RESPONSE_SERIES_REDIRECTION = 300;
	public const RESPONSE_SERIES_CLIENT_ERROR = 400;
	public const RESPONSE_SERIES_SERVER_ERROR = 500;
	
	public const VALID_RESPONSE_SERIES = [
		self::RESPONSE_SERIES_SERVER_ERROR,
		self::RESPONSE_SERIES_CLIENT_ERROR,
		self::RESPONSE_SERIES_REDIRECTION,
		self::RESPONSE_SERIES_SUCCESSFUL,
		self::RESPONSE_SERIES_INFORMATIONAL
	];
	
	public const HEADER_LOCATION = "Location";
	public const HEADER_ROBOTS = "X-Robots-Tag";
	public const HEADER_ALLOW = "Allow";
	public const HEADER_CACHE_CONTROL = "Cache-Control";
	
	public const ROBOTS_NO_INDEX_NO_FOLLOW = "noindex, nofollow";
	
	public const CACHE_CONTROL_NO_CACHE = "no-cache";
	public const CACHE_CONTROL_NO_STORE = "no-store";
	public const CACHE_CONTROL_MUST_REVALIDATE = "must-revalidate";
	public const CACHE_CONTROL_MAX_AGE = "max-age";
	
	/**
	 * The set of valid cache-control directives
	 * (More will need to be added as needed)
	 * Format:
	 *    directive-key => bool(whether the directive has a value)
	 */
	public const CACHE_CONTROL_DIRECTIVES = [
		self::CACHE_CONTROL_NO_CACHE => false,
		self::CACHE_CONTROL_NO_STORE => false,
		self::CACHE_CONTROL_MUST_REVALIDATE => false,
		self::CACHE_CONTROL_MAX_AGE => true
	];
	
	public const CACHE_CONTROL_DIRECTIVE_VALUE_SEPARATOR = "=";
	
	public const STANDARD_CACHE_CONTROL_CACHE_PREVENTION_DIRECTIVES = [
		self::CACHE_CONTROL_NO_CACHE => true,
		self::CACHE_CONTROL_NO_STORE => true,
		self::CACHE_CONTROL_MUST_REVALIDATE => true,
		self::CACHE_CONTROL_MAX_AGE => 0
	];
	
	public const QUERY_STRING_SEPARATOR = "?";
	
	/**
	 * This set should contain all status messages defined in the HTTP/1.1 RFC for standard response codes
	 * DO NOT add custom response codes here
	 * As new response codes are added to the standard, please add them here
	 * Specificition:  https://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html
	 */
	public const RESPONSE_STATUS_MESSAGES = [
		100 => "Continue",
		101 => "Switching Protocols",
		200 => "OK",
		201 => "Created",
		202 => "Accepted",
		203 => "Non-Authoritative Information",
		204 => "No Content",
		205 => "Reset Content",
		206 => "Partial Content",
		300 => "Multiple Choices",
		301 => "Moved Permanently",
		302 => "Found",
		303 => "See Other",
		304 => "Not Modified",
		305 => "Use Proxy",
		307 => "Temporary Redirect",
		400 => "Bad Request",
		401 => "Unauthorized",
		402 => "Payment Required",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Not Acceptable",
		407 => "Proxy Authentication Required",
		408 => "Request Time-out",
		409 => "Conflict",
		410 => "Gone",
		411 => "Length Required",
		412 => "Precondition Failed",
		413 => "Request Entity Too Large",
		414 => "Request URI Too Large",
		415 => "Unsupported Media Type",
		416 => "Requested range not satisfiable",
		417 => "Expectation Failed",
		500 => "Internal Server Error",
		501 => "Not Implemented",
		502 => "Bad Gateway",
		503 => "Service Unavailable",
		504 => "Gateway Time-out",
		505 => "HTTP Version not supported"
	];
	public const DEFAULT_RESPONSE_STATUS = "Unknown";
	
	public const RESPONSE_CODE_PERMANENT_REDIRECT = 301;
	public const RESPONSE_CODE_TEMPORARY_REDIRECT = 302;
	
	public const CONTENT_TYPE_JSON = "application/json";
	
	public const HEADER_DELIMITER = ":";
	
	/**
	 * Get the response series for an HTTP response code
	 * For example, a $responseCode of 204 would be an @param int $responseCode - The HTTP response code
	 * @return int - The response series(100, 200, 300, etc.). It will always be an HTTPUtil::RESPONSE_SERIES_ constant
	 * @see HTTPUtil::RESPONSE_SERIES_SUCCESS (200)
	 *                a $responseCode of 302 would be an @see HTTPUtil::RESPONSE_SERIES_REDIRECTION (300)
	 */
	public static function getResponseSeries(int $responseCode): int {
		if ($responseCode >= self::MIN_RESPONSE_CODE && $responseCode <= self::MAX_RESPONSE_CODE) {
			$sorted = self::VALID_RESPONSE_SERIES;
			arsort($sorted);
			foreach ($sorted as $series) {
				if ($responseCode >= $series) {
					return $series;
				}
			}
		}
		return self::RESPONSE_SERIES_UNRECOGNIZED;
	}
	
	/**
	 * Set the HTTP response code for this request
	 * At first it may appear that this function is a reinvention of the wheel, but this is not the case. The lack of support for custom response codes(and the poor handling of defaulting to a 500) causes the other
	 * function to be useless for many use cases. This function will work with any response code. The message is optional, but should be provided if a custom response code is used.
	 * @param int $responseCode - The HTTP response code
	 * @param string|null $message - The HTTP status message (ex. "Not Found", "Internal Server Error", "Bad Request") [Optional, Code defined in RFC/self::RESPONSE_STATUS_MESSAGES will be used or self::DEFAULT_RESPONSE_STATUS if no status defined for code]
	 */
	public static function setResponseCode(int $responseCode, string $message = null): void {
		if ($message === null || $message === '') {
			//Default to the main one for the series(for a 487, for instance, this should return "Bad Request" just as a 400)
			$message = self::RESPONSE_STATUS_MESSAGES[$responseCode] ?? self::RESPONSE_STATUS_MESSAGES[self::getResponseSeries($responseCode)] ?? self::DEFAULT_RESPONSE_STATUS;
		}
		header(($_SERVER["SERVER_PROTOCOL"] ?? "HTTP/1.0") . " " . $responseCode . " " . $message);
	}
	
	/**
	 * Set an HTTP response header
	 * @param string $field - The header field(Location, Cache-Control, X-Custom-Header, etc.)
	 * @param string $value - The header value
	 * @throws InvalidArgumentException - If field of value is invalid
	 */
	public static function setResponseHeader(string $field, string $value) {
		if (StringUtil::str_contains($field, ":")) {
			throw new InvalidArgumentException("Invalid header field specified! Field: \"$field\", Value: \"$value\"");
		}
		header($field . ": " . $value);
	}
	
	/**
	 * Compile the given set of directives to a value for the cache-control header
	 * @param array $directives - An associative array where the keys are directive names are bool if the directive is boolean or the value if the directive has a parameter
	 * Example:
	 *    [
	 *        "no-cache"=>true,
	 *        "max-age"=>0
	 *    ]
	 */
	public static function buildCacheControlDirectives(array $directives): string {
		$processedDirectives = [];
		foreach (self::CACHE_CONTROL_DIRECTIVES as $directiveName => $directiveValue) {
			if (array_key_exists($directiveName, $directives)) {
				if ($directiveValue === false && $directives[$directiveName]) {
					$processedDirectives[] = $directiveName;
				} else {
					$processedDirectives[] = $directiveName . self::CACHE_CONTROL_DIRECTIVE_VALUE_SEPARATOR . $directives[$directiveName];
				}
			}
		}
		return ArrayUtil::toDelimitedList($processedDirectives);
	}
	
	/**
	 * Set the Cache-Control response header based on the given directives
	 * @param array $directives - An associative array where the keys are directive names are bool if the directive is boolean or the value if the directive has a parameter
	 * @see HttpUtil::buildCacheControlDirectives
	 */
	public static function setCacheControlDirectives(array $directives): void {
		self::setResponseHeader(self::HEADER_CACHE_CONTROL, self::buildCacheControlDirectives($directives));
	}
	
	/**
	 * Get the request method for the current request
	 * @return string - The request method
	 */
	public static function getRequestMethod(): string {
		return $_SERVER["REQUEST_METHOD"];
	}
	
	/**
	 * Parse the request parameters from stdin
	 * This is needed for PUT/DELETE as PHP does not handle these automatically as GET/POST
	 * NOTE: This will only parse parameters in the request body with the Content-Type of application/x-www-form-urlencoded,
	 *         in other words, form data. To accomodate other methods, this should look at the content-type and parse parameters
	 *         as appropriate
	 * @return array - The request parameters
	 */
	protected function parseRequestParameters(): array {
		//TODO: Add a method to get the content type header
		//TODO: Use the content type from the request header to determine the correct way to parse the parameters
		$params = [];
		parse_str(file_get_contents("php://input"), $params);
		return $params;
	}
	
	/**
	 * Get the parameters for the current request
	 * @return array - An array containing all parameters for the current request
	 *                   that were passed in a way appropriate for the request
	 */
	public static function getRequestParameters(): array {
		switch (self::getRequestMethod()) {
			case REQUEST_METHOD_GET:
				return $_GET;
				break;
			case REQUEST_METHOD_POST:
				return $_POST;
				break;
			default:
				return (new HttpUtil)->parseRequestParameters();
				break;
		}
	}
	
	/**
	 * Get the path of the current request
	 * @return string - The current path
	 */
	public static function getRequestPath(): string {
		return explode(self::QUERY_STRING_SEPARATOR, $_SERVER["REQUEST_URI"])[0];
	}
	
}