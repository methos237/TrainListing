<?php

namespace Http;

use JsonSerializable;
use Util\StringUtil;
use Util\ArrayUtil;
use Util\Path;
use Util\ImmutablePath;

/**
 * A utility class for constructing URL's
 * (NOTE: Url objects are immutable! The set methods will return a copy of the Url with the specified parameter set and the original instance will not be modified)
 *
 * TODO: Make port null, rather than set it to 80.
 * @author James Polk
 */
class Url implements JsonSerializable {
	
	public const PROTOCOL_HTTP = "http";
	public const PROTOCOL_HTTPS = "https";
	
	public const DEFAULT_PORT_HTTP = 80;
	public const DEFAULT_PORT_HTTPS = 443;
	
	public const PROTOCOL_SEPARATOR = "://";
	public const PATH_SEPARATOR = "/";
	public const PORT_SEPARATOR = ":";
	public const ANCHOR_SEPARATOR = "#";
	public const QUERY_STRING_SEPARATOR = "?";
	public const QUERY_PARAM_SEPARATOR = "&";
	public const QUERY_VALUE_SEPARATOR = "=";
	
	public const DEFAULT_PORTS = [
		self::PROTOCOL_HTTP => self::DEFAULT_PORT_HTTP,
		self::PROTOCOL_HTTPS => self::DEFAULT_PORT_HTTPS
	];
	
	
	public const MATCH_REQUEST = -1;
	
	protected $path;
	protected array $params;
	protected $anchor;
	protected string $protocol;
	protected string $domain;
	protected $port;
	protected $absolute;
	
	public function __construct($path = "", array $params = [], $anchor = null, $protocol = self::MATCH_REQUEST, $domain = null, $port = self::MATCH_REQUEST, $absolute = false) {
		$this->path = self::evaluatePath($path);
		$this->params = $params;
		$this->anchor = $anchor;
		$this->protocol = self::evaluateProtocol($protocol);
		$this->domain = self::evaluateDomain($domain);
		$this->port = self::evaluatePort($port, $this->protocol);
		$this->absolute = $absolute;
	}
	
	/**
	 * Clone this object and call $func in the context of the cloned object
	 * @param callable $func - The function to run
	 * @return Url - The cloned object
	 */
	protected function cloneRun(callable $func): Url {
		$cp = clone $this;
		$func = $func->bindTo($cp);
		$func();
		return $cp;
	}
	
	protected static function evaluatePath($path): Path {
		return ImmutablePath::fromUrlPath(($path === self::MATCH_REQUEST ? explode("?", $_SERVER["REQUEST_URI"])[0] : $path));
	}
	
	public function setPath($path = ""): Url {
		return $this->cloneRun(function () use ($path) {
			$this->path = self::evaluatePath($path);
		});
	}
	
	public function getPath(): Path {
		return $this->path;
	}
	
	/**
	 * Merges the given parameters with any pre-existing
	 * @param array $params - An associative array of parameter keys=>values
	 * @return Url - a clone of this Url after this operation is run
	 */
	public function mergeParams(array $params = []): Url {
		return $this->cloneRun(function () use ($params) {
			$this->params = array_merge($this->params, $params);
		});
	}
	
	/**
	 * Set the parameters for this Url
	 * @param array $params - An associative array of parameter keys=>values
	 * @return Url - a clone of this Url after this operation is run
	 */
	public function setParams(array $params = []): Url {
		$obj = $this->cloneRun(function () {
			$this->params = [];
		});
		foreach ($params as $key => $value) {
			$obj = $obj->setParam($key, $value);
		}
		return $obj;
	}
	
	public function getParams(): array {
		return $this->params;
	}
	
	/**
	 * Set the value of a request parameter
	 * NOTE: If $value is null, then the parameter with the given key will be removed
	 * @param string $key - The parameter name
	 * @param mixed value - The parameter value(or null to remove the parameter)
	 * @return Url - a clone of this Url after this operation is run
	 */
	public function setParam($key, $value): Url {
		if ($value === null) {
			return $this->removeParams($key);
		}
		
		return $this->cloneRun(function () use ($key, $value) {
			$this->params[$key] = $value;
		});
	}
	
	public function removeParams(...$keys): Url {
		return $this->cloneRun(function () use ($keys) {
			foreach ($keys as $key) {
				// No need to check for existence first. No problem if the array key doesn't exist.
				unset($this->params[$key]);
			}
		});
	}
	
	/**
	 * Get the value for the parameter with the given key
	 * @param $key - The parameter name
	 * @return mixed - The value for that key or null if the key does not exist
	 */
	public function getParam($key) {
		return $this->params[$key] ?? null;
	}
	
	/**
	 * Check if the URL has the specified parameter
	 * @param $key - The parameter name
	 * @return bool - True if the parameter is set, false otherwise
	 */
	public function hasParam(string $key): bool {
		return array_key_exists($key, $this->params);
	}
	
	public function setAnchor($anchor = null): Url {
		return $this->cloneRun(function () use ($anchor) {
			$this->anchor = $anchor;
		});
	}
	
	public function getAnchor() {
		return $this->anchor;
	}
	
	protected static function evaluateProtocol($protocol): string {
		return ($protocol === null ? self::PROTOCOL_HTTP : ($protocol === self::MATCH_REQUEST ? ((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? self::PROTOCOL_HTTPS : self::PROTOCOL_HTTP) : strtolower($protocol)));
		
	}
	
	public function setProtocol($protocol = self::PROTOCOL_HTTP): Url {
		return $this->cloneRun(function () use ($protocol) {
			$this->protocol = self::evaluateProtocol(strtolower($protocol));
		});
	}
	
	public function getProtocol(): string {
		return $this->protocol;
	}
	
	/**
	 * Remove the port portion(anything after and including the first, if existent, colon) from a hostname
	 * (Intended for removing port from $_SERVER["HTTP_HOST"] to retrieve just the hostname)
	 * @param string $hostname - The hostname that may or may not include a port number
	 * @return string - The hostname with the port section removed if it was present
	 */
	private static function removePort($hostname): string {
		if (StringUtil::str_contains($hostname, self::PORT_SEPARATOR)) {
			return explode(self::PORT_SEPARATOR, $hostname)[0];
		}
		return $hostname;
	}
	
	protected static function evaluateDomain($domain): string {
		return (($domain === null || $domain === self::MATCH_REQUEST) ? self::removePort($_SERVER["HTTP_HOST"]) : $domain);
	}
	
	public function setDomain($domain = null): Url {
		return $this->cloneRun(function () use ($domain) {
			$this->domain = self::evaluateDomain($domain);
		});
	}
	
	public function getDomain(): string {
		return $this->domain;
	}
	
	protected static function evaluatePort($port, $protocol = self::PROTOCOL_HTTP) {
		return ($port === null ? self::getDefaultPort($protocol) : ($port === self::MATCH_REQUEST ? (self::evaluateProtocol(self::MATCH_REQUEST) === self::PROTOCOL_HTTPS && $_SERVER["SERVER_PORT"] == self::getDefaultPort(self::PROTOCOL_HTTP) ? self::getDefaultPort(self::PROTOCOL_HTTPS) : $_SERVER["SERVER_PORT"]) : $port)); //NOTE: If SERVER_PORT is 80 and the protocol is HTTPS, this will default to 443. This is a workaround to unusual configuration on the webservers wherein the load balancer(HAProxy) handles SSL connections and then makes HTTP connections over port 80 to the webserver and then forwards that response to the client over HTTPS on 443. Because of this, the webservers will always see port 80 and HTTP even on HTTPS connections. Until there is a solution for this, the workaround will be necessary. The only caveat with this is that you can not run HTTPS on port 80, but that is very unusual so I doubt it will ever be an issue, but this code is still not ideal. If that configuration is ever changed, go ahead and remove that work around. - AKEnion 08/18/17
	}
	
	public function setPort($port = self::DEFAULT_PORT_HTTP): Url {
		return $this->cloneRun(function () use ($port) {
			$this->port = self::evaluatePort($port, $this->protocol);
		});
	}
	
	public function getPort() {
		return $this->port;
	}
	
	public function setAbsolute($absolute): Url {
		return $this->cloneRun(function () use ($absolute) {
			$this->absolute = $absolute;
		});
	}
	
	public function isAbsolute() {
		return $this->absolute;
	}
	
	/**
	 * Get the default port for a given protocol
	 * (Example: $protocol="http", return value: 80)
	 * @param string protocol - The protocol name
	 * @return int - The corresponding port or -1 if protocol not recognized
	 */
	public static function getDefaultPort($protocol = self::PROTOCOL_HTTP): int {
		$protocol = strtolower($protocol);
		return (array_key_exists($protocol, self::DEFAULT_PORTS) ? self::DEFAULT_PORTS[$protocol] : -1);
	}
	
	/**
	 * Check if a port is the default port for a protocol
	 * @param string $protocol - The protocol name
	 * @param int $port - The port number
	 * @return boolean - True if the default port, false otherwise
	 */
	public static function isDefaultPort(string $protocol, int $port): bool {
		return self::getDefaultPort($protocol) === $port && $port !== -1;
	}
	
	public function hasTrailingSlash(): bool {
		return $this->path->hasTrailingSlash();
	}
	
	public function setTrailingSlash(bool $trailingSlash = true): Url {
		return $this->setPath($this->path->setTrailingSlash($trailingSlash));
	}
	
	/**
	 * Build a Url from the current state of this builder
	 * @param boolean|null $absolute - [Optional] Whether or not the returned Url should be absolute(this overrides the property on the builder if specified, otherwise the absolute property at the object level is used)
	 * @param boolean $escapeHTML - [Optional] Set to true to escape the URL for use in HTML attributes
	 * @return string - The URL
	 */
	public function buildUrl(?bool $absolute = null, bool $escapeHTML = true): string {
		$url = "";
		if ($absolute ?? $this->absolute) {
			$url .= $this->protocol . self::PROTOCOL_SEPARATOR . $this->domain;
			if (!self::isDefaultPort($this->protocol, $this->port)) {
				$url .= self::PORT_SEPARATOR . $this->port;
			}
		}
		if (!StringUtil::startsWith($this->path, self::PATH_SEPARATOR)) {
			$this->path = self::evaluatePath(self::PATH_SEPARATOR . $this->path);
		}
		$url .= $this->path->getFullPath(true);
		if (count($this->params) > 0) {
			$url .= self::QUERY_STRING_SEPARATOR;
			$first = true;
			foreach ($this->params as $key => $value) {
				if (!$first) {
					$url .= self::QUERY_PARAM_SEPARATOR;
				}
				$key = rawurlencode((string)$key);
				if (is_array($value)) {
					if ($escapeHTML) {
						$key = htmlspecialchars($key) . "[]";
					}
					$arrayFirst = true;
					foreach ($value as $v) {
						if (!$arrayFirst) {
							$url .= self::QUERY_PARAM_SEPARATOR;
						}
						$v = rawurlencode((string)$v);
						if ($escapeHTML) {
							$v = htmlspecialchars($v);
						}
						$url .= $key . self::QUERY_VALUE_SEPARATOR . $v;
						if ($arrayFirst) {
							$arrayFirst = false;
						}
					}
				} else {
					$value = rawurlencode((string)$value);
					if ($escapeHTML) {
						$key = htmlspecialchars($key);
						$value = htmlspecialchars($value);
					}
					$url .= $key;
					if ($value !== '') $url .= self::QUERY_VALUE_SEPARATOR . $value;
				}
				if ($first)
					$first = false;
			}
		}
		if ($this->anchor !== null)
			$url .= self::ANCHOR_SEPARATOR . rawurlencode($this->anchor);
		return $url;
	}
	
	/**
	 * Make this Url relative to the provided URL
	 * (This will combine the paths and will set the host/port based on the provided URL)
	 * @param Url $url - The URL to which this URL should be made relative
	 * @return Url - A new URL based on the current URL that is relative to the provided URL
	 */
	public function relativeTo(Url $url): Url {
		return $this->setDomain($url->getDomain())
			->setPort($url->getPort())
			->setPath(new ImmutablePath($url->getPath(), $this->getPath()))
			->setProtocol($url->getProtocol())
			->setAbsolute($url->isAbsolute());
	}
	
	public function __clone() {
		$this->params = ArrayUtil::deepCopy($this->params);
	}
	
	public function __toString() {
		return $this->buildUrl();
	}
	
	/**
	 * Construct a new Url with the same parameters as the current HTTP request that initiated the script(assuming that this is running under a web server
	 * @return Url
	 */
	public static function fromRequest(): Url {
		return new Url(self::MATCH_REQUEST, $_GET, null, self::MATCH_REQUEST, self::MATCH_REQUEST, self::MATCH_REQUEST, true);
	}
	
	public static function parseQueryString($queryString): array {
		$params = [];
		$components = explode(self::QUERY_PARAM_SEPARATOR, $queryString);
		foreach ($components as $component) {
			if (!$component) continue;
			if (StringUtil::contains($component, self::QUERY_VALUE_SEPARATOR)) {
				$kvPair = explode(self::QUERY_VALUE_SEPARATOR, $component);
				$params[urldecode($kvPair[0])] = urldecode($kvPair[1]);
			} else {
				$params[urldecode($component)] = '';
			}
		}
		return $params;
	}
	
	public static function fromUrl($url): ?Url {
		if (preg_match("/^https?:\\/\\//i", $url)) {
			$components = parse_url($url);
			if ($components === false)
				return null;
			$protocol = ArrayUtil::get($components, "scheme", self::PROTOCOL_HTTP);
			return new Url(
				ArrayUtil::get($components, "path", ""),
				self::parseQueryString(ArrayUtil::get($components, "query", "")),
				ArrayUtil::get($components, "fragment"),
				$protocol,
				ArrayUtil::get($components, "host", null),
				ArrayUtil::get($components, "port", self::getDefaultPort($protocol)),
				true
			);
		} else {
			$urlBuilder = new Url();
			$parts = explode(self::QUERY_STRING_SEPARATOR, $url, 2);
			$partCount = count($parts);
			if ($partCount > 0) {
				$urlBuilder = $urlBuilder->setPath($parts[0]);
				if ($partCount == 2) {
					$parts = explode(self::ANCHOR_SEPARATOR, $parts[1], 2);
					$urlBuilder = $urlBuilder->setParams(self::parseQueryString($parts[0]));
					if (count($parts) == 2)
						$urlBuilder = $urlBuilder->setAnchor($parts[1]);
				}
			}
			return $urlBuilder->setAbsolute(false);
		}
	}
	
	/**
	 * Sanitizes strings for use in URLs
	 * 1. Trim surrounding whitespace
	 * 2. Replace ampersands with "-and-"
	 * 3. Remove apostrophes
	 * 4. Replace any character that isn't alphanumeric, a space, or a hyphen with a hyphen
	 * 5. Deduplicate spaces and replace with a hyphen
	 * 6. Deduplicate hyphens
	 * 7. Trim surrounding hyphens
	 * @return string|null
	 */
	public static function sanitizeString(?string $input): ?string {
		return ($input) ?
			strtolower(
				trim(
					preg_replace('/-+/', '-',
						preg_replace('/\s+/', '-',
							preg_replace('/[^a-zA-Z0-9 -]/', '-',
								str_replace("'", '',
									str_replace('&', '-and-',
										trim($input)
									)
								)
							)
						)
					),
					'-')
			)
			: null;
	}
	
	public function jsonSerialize() {
		return (string)$this;
	}
	
}