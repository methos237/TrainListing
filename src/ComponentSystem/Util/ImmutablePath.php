<?php

namespace Util;

/**
 * An immutable or read-only path
 *
 * All set operations will throw a PathException.
 *
 * @author James Polk
 */
class ImmutablePath extends Path {
	
	public function __construct(...$components) {
		parent::__construct($components);
	}
	
	/**
	 * @throws PathException
	 */
	public function offsetSet($offset, $value) {
		throw new PathException("Path is immutable, components cannot be set");
	}
	
	/**
	 * @throws PathException
	 */
	public function offsetUnset($offset) {
		throw new PathException("Path is immutable, components cannot be unset");
	}
	
	public function setTrailingSlash(bool $trailingSlash): Path {
		$clone = clone $this;
		if ($clone->getLength() > 0) {
			$clone->components[$this->getLength() - 1]->setTrailingSlash($trailingSlash);
		}
		return $clone;
	}
	
}