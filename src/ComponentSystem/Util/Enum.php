<?php

namespace Util;

use ReflectionClass;

/**
 * A foundation class for the implementation of an enum(SplEnum is not available)
 *
 * Usage: Create a final class that extends the Enum class and has a private constructor. Define
 * a class constant in the child class for each value in the enum. The value of each constant
 * should be an array of parameters that can be used to instantiate the child class.
 *
 * For IDE type completion, add [at symbol]method static ClassName CONSTANT_NAME() to class phpDoc for each constant
 *
 * @author James Polk
 */
abstract class Enum {
	
	/**
	 * All enum values are stored in this associative array keyed by the fully-qualified class name
	 */
	private static $values = [];
	
	private $key;
	
	public static function __callStatic($key, $args): Enum {
		if (count($args) > 0)
			throw new EnumException("No arguments expected");
		$value = static::forKey($key);
		if ($value === null)
			throw new EnumException("Property $key does not exist");
		return $value;
	}
	
	/**
	 * Check if a key exists in the enum
	 * @param string $key - The key for which to search
	 * @return bool - Whether or not the key exists
	 */
	public static function hasKey(string $key): bool {
		return array_key_exists($key, self::getValues());
	}
	
	/**
	 * Look up a value by key
	 * @param string - the key
	 * @return ?Enum - The value or null if the key does not exist
	 */
	public static function forKey(string $key): ?Enum {
		return self::getValues()[$key] ?? null;
	}
	
	/**
	 * Get all of the enum keys that exist
	 */
	public static function getKeys(): array {
		return array_keys(self::getValues());
	}
	
	/**
	 * Get the values that exist in the enum
	 * @return array - An associative array of the enum
	 */
	public static function getValues(): array {
		$class = get_called_class();
		if ($class == self::class)
			throw new EnumException("Enum methods should not be called directly on Enum class");
		if (!array_key_exists($class, self::$values))
			self::$values[$class] = self::loadValues($class);
		return self::$values[$class];
	}
	
	/**
	 * Instantiate all enum values from class constants
	 * @return array - An associative array of the enum values(constant key=>instance)
	 */
	private static function loadValues(string $class): array {
		$reflector = new ReflectionClass($class);
		if (!$reflector->isFinal())
			throw new EnumException("Enum classes must be final");
		$constructor = $reflector->getConstructor();
		if ($constructor === null)
			throw new EnumException("No constructor defined");
		if (!$constructor->isPrivate())
			throw new EnumException("Enum constructor must be private");
		$constructor->setAccessible(true);
		$values = [];
		foreach ($reflector->getConstants() as $key => $value) {
			$values[$key] = $reflector->newInstanceWithoutConstructor();
			$constructor->invoke($values[$key], ...$value);
			$values[$key]->key = $key;
		}
		return $values;
	}
	
	/**
	 * Compare the position of the current element to the specified element
	 * @param self $compared - The element to be compared
	 * @return int - -1, 0, or 1, respectively, if the current element is earlier, equal, or later than the compared element
	 */
	public function compare(self $compared): int {
		if ($compared == $this)
			return 0;
		foreach (static::getValues() as $value) {
			if ($value == $this)
				return -1;
			if ($value == $compared)
				return 1;
		}
		throw new EnumException("Invalid Enum element specified");
	}
	
	public final function getKey(): string {
		return $this->key;
	}
	
	public function __toString(): string {
		return $this->getKey();
	}
	
}