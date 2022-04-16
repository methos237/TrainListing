<?php

namespace Http;

/**
 * An HTTP response indicating redirection
 * @author James Polk
 */
class RedirectHttpResponse extends HttpResponse {
	
	public function __construct(string $destination, $responseCode = 302) {
		parent::__construct($responseCode, null, [
			HttpUtil::HEADER_LOCATION => $destination,
			HttpUtil::HEADER_CACHE_CONTROL => HttpUtil::buildCacheControlDirectives(HttpUtil::STANDARD_CACHE_CONTROL_CACHE_PREVENTION_DIRECTIVES)
		]);
	}
	
}