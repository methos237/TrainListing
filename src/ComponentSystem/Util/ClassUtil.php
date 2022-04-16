<?php

namespace Util;

/**
 * Utilities related to PHP classes
 * @author James Polk
 */
class ClassUtil {
	
	public const MAGIC_METHOD_TO_STRING = "__toString";
	
	/**
	 * Checks if $object is an instanceof $class
	 * This differs from instanceof or is_a in that it will not return true if the object is an instance of a parent class of $class
	 * @param Object $object - The object to test
	 * @param String $class - The fully qualified class name to check if $object is an instance of
	 * @return boolean - Whether $object is an instance of $class
	 */
	public static function isClass(object $object, string $class): bool {
		if (StringUtil::startsWith($class, "\\") && strlen($class) > 1) {
			$class = substr($class, 1);
		}
		return get_class($object) === $class;
	}
	
	/**
	 * Remove a namespace from a class name to get just the class name
	 * @param string $class - The class name(with or without namespace)
	 * @return string - The class name without a namespace
	 */
	public static function removeNamespaceFromClassName(string $class): string {
		if (StringUtil::str_contains($class, "\\")) {
			$matches = [];
			preg_match("/^(.+\\\\)?(.+)$/", $class, $matches);
			return $matches[2];
		}
		return $class;
	}
	
	/**
	 * Check if the given object can be cast to a string
	 * @param Object $object - The object to check
	 * @return bool - Whether the object can be cast to a string
	 */
	public static function allowsStringCast(object $object): bool {
		return method_exists($object, self::MAGIC_METHOD_TO_STRING);
	}
	
}