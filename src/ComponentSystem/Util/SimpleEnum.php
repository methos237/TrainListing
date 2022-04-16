<?php

namespace Util;

use ReflectionClass;
use ReflectionException;
use TypeError;

/**
 * Class SimpleEnum
 *
 * Simple enum declaration class
 *
 * @author Unknown - sourced from https://3v4l.org/ZQhMn
 * via https://www.reddit.com/r/PHP/comments/6it21f/why_are_there_no_proper_enums_in_php_are_they/dj8yrup/
 *
 * Usage: Create new class that extends SimpleEnum as follows:
 * <code>
 * /**
 *  /@method static RED()
 *  /@method static GREEN()
 *  /@method static BLUE()
 * {@*}
 * class Color extends Enum {}
 * </code>
 */
abstract class SimpleEnum {
	
	private $name;
	private static $enums;
	
	private function __construct($name) {
		$this->name = $name;
	}
	
	/**
	 * Returns an assoc. array of ['ENUM_NAME' => $ENUM_VALUE] for all enum values.
	 * @return array
	 * @throws EnumException
	 * @throws ReflectionException
	 */
	public static function getAll(): array {
		$class = static::class;
		if (!isset(self::$enums[$class])) {
			static::init();
		}
		return self::$enums[$class];
	}
	
	/**
	 * Return an enum value (object) from a string name.
	 * @param $name
	 * @return $this
	 * @throws EnumException
	 * @throws ReflectionException
	 */
	public static function fromString($name): self {
		return static::__callStatic($name, []);
	}
	
	public function __toString() {
		return $this->name;
	}
	
	/**
	 * @param $name
	 * @param $args
	 * @return mixed
	 * @throws EnumException
	 * @throws ReflectionException
	 */
	public static function __callStatic($name, $args) {
		$class = static::class;
		if (!isset(self::$enums[$class])) {
			static::init();
		}
		if (!isset(self::$enums[$class][$name])) {
			throw new TypeError('Undefined enum ' . $class . '::' . $name . '()');
		}
		return self::$enums[$class][$name];
	}
	
	/**
	 * @throws EnumException
	 * @throws ReflectionException
	 */
	private static function init(): void {
		$class = static::class;
		
		if ($class === __CLASS__) {
			throw new EnumException('Do not invoke methods directly on class Enum.');
		}
		
		$doc = (new ReflectionClass($class))->getDocComment();
		
		if (preg_match_all('/@method\s+static\s+(\w+)/i', $doc, $matches)) {
			foreach ($matches[1] as $name) {
				self::$enums[$class][$name] = new static($name);
			}
		} else {
			throw new EnumException('Please provide a PHPDoc for ' . $class . ' with a static @method for each enum value.');
		}
	}
}