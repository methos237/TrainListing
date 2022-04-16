<?php

namespace Http;

/**
 * Controller
 * A base class that can be extended to function as the controller for an MVC structure
 * @author James Polk
 */
abstract class Controller {
	
	const HTTP_TEMPORARY_REDIRECT = 302;
	const HTTP_PERMANENT_REDIRECT = 301;
	
	protected HttpRequest $request;
	
	/**
	 * Create a new Controller
	 * @param HttpRequest|null $request - The request
	 */
	public function __construct(HttpRequest $request = null) {
		$this->request = $request ?? HttpRequest::current();
	}
	
	/**
	 * Get the request parameters
	 */
	public function getParameters(): array {
		return $this->request->getParams();
	}
	
	/**
	 * Get a request parameter
	 * @param array|string $key - The parameter key(or possible keys, in order of precedence, if $key is an array)
	 * @param mixed $def - The value to return if $key does not exist in the parameter set
	 * @return mixed - the first parameter value found or the default value
	 */
	public function getParameter($key, $def = null) {
		if (is_array($key)) {
			foreach ($key as $k) {
				if ($this->request->hasParam($k)) {
					return $this->request->getParam($k);
				}
			}
		} else if ($this->request->hasParam($key)) {
			return $this->request->getParam($key);
		}
		return $def;
	}
	
	/**
	 * Check if a parameter is set
	 * @param string $key - The parameter key
	 * @return boolean - Whether the parameter is set
	 */
	public function hasParameter(string $key): bool {
		return $this->request->hasParam($key);
	}
	
	/**
	 * Get the HTTP method for the current request
	 * @return string - The request method
	 */
	final protected function getRequestMethod(): string {
		return $this->request->getMethod();
	}
	
	/**
	 * Handle a given request
	 * @return HttpResponse|null - A response or null if the Controller does not have a response for the request
	 */
	abstract public function handleRequest(): ?HttpResponse;
	
	/**
	 * Handle and respond to the request
	 */
	public function respond(): void {
		$response = $this->handleRequest();
		if ($response !== null) {
			$response->send();
		}
		exit;
	}
	
}