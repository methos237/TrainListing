<?php

namespace Util;

use ArrayAccess;
use Countable;

/**
 * A path(either for a URI or a file)
 * @author James Polk
 */
class Path implements ArrayAccess, Countable {
	
	protected $components = [];
	
	/**
	 * Create a new path from the given components
	 * @param ...$components - The components
	 * Components can be either a string representing an individual path
	 * component, a full path string, a partial path string, another
	 * Path object, or an array containing any combination of these
	 * items.
	 * Components will be combined in the provided order in order
	 * to form the full path.
	 * @throws PathException
	 */
	public function __construct(...$components) {
		$this->components = $this->parseComponent($components);
		$this->checkSeparators(); //TODO: There has to be a better way to handle this
	}
	
	/**
	 * Parse a path component
	 * @throws PathException - if unable to parse component
	 *
	 * TODO refactor parseComponent with ValueComponent and SlashComponent objects
	 */
	protected function parseComponent($component, bool $urlDecode = false): array {
		if (is_array($component)) {
			$components = [];
			foreach ($component as $c) {
				$components = array_merge($components, $this->parseComponent($c, $urlDecode));
			}
			return $components;
		}
		if ($component instanceof PathComponent) {
			return [clone $component];
		}
		if ($component instanceof Path) {
			return $this->parseComponent($component->components, $urlDecode);
		}
		if (is_object($component) && !ClassUtil::allowsStringCast($component)) {
			throw new PathException("Object cannot be cast to string: " . print_r($component, true));
		}
		$value = (string)$component;
		$trailingSlash = StringUtil::endsWith($value, PathUtil::PATH_SEPARATOR);
		$precedingSlash = StringUtil::startsWith($value, PathUtil::PATH_SEPARATOR);
		$components = [];
		if ($value === '/') {
			$components[] = new PathComponent('', true, false);
		} else {
			$split = PathUtil::splitComponents($value);
			$last = count($split) - 1;
			foreach ($split as $key => $value) {
				$components[] = new PathComponent($urlDecode ? urldecode($value) : $value, ($key === $last) ? $trailingSlash : true, $key === 0 ? $precedingSlash : true);
			}
		}
		return $components;
	}
	
	protected function checkSeparators(): void {
		$last = count($this->components) - 1;
		foreach ($this->components as $key => &$value) {
			if ($key > 0) {
				$value->setPrecedingSlash(true);
			}
			if ($key < $last) {
				$value->setTrailingSlash(true);
			}
		}
	}
	
	public function getComponents(): array {
		return array_map(static function ($value) {
			return (string)$value;
		}, $this->components); //Convert all values to strings when returning so internal PathComponents are not exposed
	}
	
	public function setTrailingSlash(bool $trailingSlash): Path {
		if ($this->getLength() > 0) {
			$this->components[$this->getLength() - 1]->setTrailingSlash($trailingSlash);
		}
		return $this;
	}
	
	public function hasTrailingSlash(): bool {
		if ($this->getLength() > 0) {
			return $this->components[$this->getLength() - 1]->hasTrailingSlash();
		}
		return true;
	}
	
	public function hasPrecedingSlash(): bool {
		if ($this->getLength() > 0) {
			return $this->components[0]->hasPrecedingSlash();
		}
		return false;
	}
	
	public function getFullPath(bool $urlEncode = false): string {
		return UrlUtil::assemblePath(($this->hasTrailingSlash() ? PathUtil::MODE_TRAILING_SLASH : 0) | ($this->hasPrecedingSlash() ? PathUtil::MODE_PRECEDING_SLASH : 0) | ($urlEncode ? UrlUtil::MODE_URL_ENCODE : 0), ...$this->components);
	}
	
	public function offsetExists($offset): bool {
		return array_key_exists($offset, $this->components);
	}
	
	public function offsetGet($offset): ?string {
		return $this->components[$offset] ?? null;
	}
	
	public function offsetSet($offset, $value) {
		if ($offset >= $this->getLength()) {
			$this->components = array_merge($this->components, $this->parseComponent($value));
		} else {
			$before = array_slice($this->components, 0, $offset);
			$after = array_slice($this->components, $offset + 1);
			$this->components = array_merge($before, $this->parseComponent($value), $after);
		}
		$this->checkSeparators();
	}
	
	/**
	 * @throws PathException
	 */
	public function offsetUnset($offset) {
		if (!array_key_exists($offset, $this->components)) {
			throw new PathException("Invalid offset: $offset");
		}
		unset($this->components[$offset]);
		$this->components = array_values($this->components);
		$this->checkSeparators();
	}
	
	public function getLength(): int {
		return count($this->components);
	}
	
	public function count(): int {
		return $this->getLength();
	}
	
	public function getLast(): ?string {
		return $this->components[$this->getLength() - 1];
	}
	
	/**
	 * Truncate the path so that it has no more components than the specified length
	 * @param int $length - The length to which to truncate the path
	 * @return Path - The truncated path(the original is not modified)
	 */
	public function truncate(int $length): Path {
		$components = [];
		foreach ($this->components as $component) {
			$components[] = $component;
			if (--$length === 0) {
				break;
			}
		}
		return new Path(...$components);
	}
	
	public function __toString(): string {
		return $this->getFullPath();
	}
	
	/**
	 * Compare this path to another path
	 * @param Path $path - The other path to check
	 * @param bool $checkSeparators - Whether or not to compare (trailing/leading) separators (Default: false)
	 * @return bool - Whether or not the paths are equivalent
	 */
	public function compare(Path $path, bool $checkSeparators = false): bool {
		if ($this->getLength() !== $path->getLength()) {
			return false;
		}
		foreach ($this->getComponents() as $index => $component) {
			if ($path[$index] !== $component) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Check if this path resides under the specified path
	 * @param Path $path - The path to which to compare this path
	 * @param bool $matchSelf - If true, return true when the paths are equal
	 * @return bool - True if this path is a child of the provided path, false otherwise
	 * Examples:
	 *    /base/child is a child of /base
	 *  /base/child/grandchild is a child of /base and /base/child
	 */
	public function isChildOf(Path $path, bool $matchSelf = true): bool {
		if (!$matchSelf && $this->getLength() === $path->getLength()) {
			return false;
		}
		return $path->compare($this->truncate($path->getLength()));
	}
	
	/**
	 * Create a new path from a given URL path
	 * @param string $urlPath - The URL path
	 * @return Path - A Path object based on the provided URL path
	 * @throws PathException
	 */
	public static function fromUrlPath(string $urlPath): self {
		$path = new static();
		$path->components = $path->parseComponent($urlPath, true);
		$path->checkSeparators();
		return $path;
	}
	
}