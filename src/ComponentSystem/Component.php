<?php

namespace ComponentSystem;

use Http\HttpResponse;
use Http\HttpUtil;

/**
 * A Component is the base building block of a page. A Container is made up of one or more components,
 * and a Page is made up of one or more Containers or Components. Each Component is a self-contained View and
 * contains any dependencies (such as css or javaScript files) that it needs in order to be displayed and its own
 * TTL for caching purposes.
 */
abstract class Component {
	
	public const DEFAULT_SHORT_CACHE_LIFETIME = 900; // 900 seconds = 15 minutes
	
	/**
	 * Present this component to STDOUT
	 */
	abstract public function display(): void;
	
	/**
	 * List all the <head> element content that this component depends on such as css of javaScript files
	 * @return array - list of <head> HTML. May contain file paths or string-encoded scripts
	 */
	public function getDependencies(): array {
		return [];
	}
	
	/**
	 * Get the lifetime (for caching purposes) of this component
	 * @return int - the TTL for this component, in seconds
	 */
	public function getLifetime(): int {
		return self::DEFAULT_SHORT_CACHE_LIFETIME;
	}
	
	/**
	 * Create an HTTP response wrapping this component
	 * @param int $status - status code to use
	 * @param array $headers - any additional headers to use
	 * @return HttpResponse
	 */
	public function toHttpResponse(int $status = 200, array $headers = []): HttpResponse {
		$lifetime = $this->getLifetime();
		if ($lifetime > 0) {
			$headers["Cache-Control"] = "max-age=$lifetime";
		} else {
			$headers["Cache-Control"] = HttpUtil::buildCacheControlDirectives(HttpUtil::STANDARD_CACHE_CONTROL_CACHE_PREVENTION_DIRECTIVES);
		}
		return new HttpResponse($status, $this, $headers);
	}
	
	/**
	 * Create a string representation of this component
	 * @return string
	 */
	public function __toString(): string {
		ob_start();
		$this->display();
		return (string) ob_get_clean();
	}
	
}