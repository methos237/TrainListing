<?php

namespace Util;

use ArrayAccess;
use Exception;

/**
 * ArrayUtil
 * Contains utility methods relating to or operating on arrays
 *
 * @author James Polk
 */
class ArrayUtil {
	
	private const DEFAULT_DELIMITER = ", ";
	private const DEFAULT_VALUE = "";
	private const PATH_DELIMITER = ".";
	
	/**
		Makes a deep copy of the array all objects within it (recursively)
		@param array $a - The array to copy
		@return array - A deep copy of $a
	*/
	public static function deepCopy(array $a) {
		$copy = array();
		foreach ($a as $key => $value) {
			$copy[$key] = (is_array($value) ? self::deepCopy($value) : (is_object($value) ? (clone $value) : $value));
		}
		return $copy;
	}
	
	/**
		Makes a delimited list of the values in the array.
		Note: *All array elements must implement the __toString method
			  *This is not a recursive operation!
		@param array $a - The array on which to operate
		@param string $delimiter - [Optional, Defaults to ArrayUtil::DEFAULT_DELIMITER] The delimiter to be used in building the list
		@return string - A string representing the provided array in a list delimited by $delimiter
	*/
	public static function toDelimitedList(array $a, $delimiter = self::DEFAULT_DELIMITER) {
		$string = "";
		$first = true;
		foreach ($a as $v) {
			if (!$first)
				$string .= $delimiter;
			$string .= (string)$v;
			$first = false;
		}
		return $string;
	}
	
	/**
	 * Parse a delimited list into an array
	 *
	 * @param string $list - The list to parse
	 * @param string $delimiter - [Optional, Defaults to ArrayUtil::DEFAULT_DELIMITER] The delimiter to be used in building the list
	 * @param int|null $size - [Optional, Defaults to null] The number of elements to be returned
	 * @return array<string> - The parsed array
	 *
	 */
	public static function parseDelimitedList(string $list, string $delimiter = self::DEFAULT_DELIMITER, ?int $size = null, $defaultValue = null): array {
		$a = explode($delimiter, $list, ($size === null ? PHP_INT_MAX : $size));
		if ($size !== null) {
			$len = count($a);
			while ($len < $size) {
				$a[] = $defaultValue;
				$len++;
			}
		}
		return $a;
	}
	
	/**
	 * Get a value for a key that may or may not exist in an array, returning a default in the latter case
	 * (Note: unlike some methods in ArrayUtil, this will work with arrays or objects that implement ArrayAccess)
	 * @param array|ArrayAccess $array - The array
	 * @param $key - The array index/key
	 * @param $def - [Optional; Defaults to null] The default value to be returned if the key does not exist
	 */
	public static function get($array, $key, $def = null) {
		if ((is_array($array) && array_key_exists($key, $array)) || ($array instanceof ArrayAccess && $array->offsetExists($key))) {
			return $array[$key];
		}
		return $def;
	}
	
	/**
	 * Initialize an array with $length elements set to $value
	 *
	 * @param int $length - The length of the array to allocate
	 * @param $value - The value for each index in the array, default: null
	 * @return array
	 */
	public static function allocate(int $length, $value = null): array {
		$array = [];
		for ($i = 0; $i < $length; $i++) {
			$array[$i] = $value;
		}
		return $array;
	}
	
	/**
	 * Resolve the specified "path" to an element in an array
	 * @param string $path - The path in the array('.' separated, as object properties can be accessed in JavaScript), a single dot('.') will resolve to the root element
	 * @param array $array - The array in which to search
	 * @return mixed - A reference to the element or null if not found
	 * @throws Exception - If the specified path does not exist in the array
	 */
	public static function &resolvePath(string $path, array &$array) {
		$element =& $array;
		$components = explode(self::PATH_DELIMITER, $path);
		foreach ($components as $component) {
			if (empty($component)) {
				continue;
			}
			if (!array_key_exists($component, $element)) {
				throw new Exception("Path {$path} does not exist in array");
			}
			$element =& $element[$component];
		}
		return $element;
	}
	
	/**
	 * Provides the difference between two given arrays recursively
	 *
	 * @author Jan Hartigan <https://stackoverflow.com/users/385950/treeface>
	 * @param $arr1
	 * @param $arr2
	 * @return array
	 */
	public static function array_diff_recursive($arr1, $arr2): array {
		$outputDiff = [];
		foreach ($arr1 as $key => $value) {
			if (array_key_exists($key, $arr2)) {
				if (is_array($value)) {
					$recursiveDiff = self::array_diff_recursive($value, $arr2[$key]);
					
					if (count($recursiveDiff)) {
						$outputDiff[$key] = $recursiveDiff;
					}
				} elseif (!in_array($value, $arr2, true)) {
					$outputDiff[$key] = $value;
				}
			} elseif (!in_array($value, $arr2, true)) {
				$outputDiff[$key] = $value;
			}
		}
		return $outputDiff;
	}
	
	/**
	 * Stable uasort with equal vales remaining in the same order
	 *
	 * @author Martijn van der Lee <https://github.com/vanderlee/PHP-stable-sort-functions>
	 * @license MIT Open Source
	 * @param array $array
	 * @param callable $value_compare_func
	 * @return bool
	 */
	public static function uasort(array &$array, callable $value_compare_func): bool {
		$index = 0;
		foreach ($array as &$item) {
			$item = array($index++, $item);
		}
		$result = uasort($array, static function ($a, $b) use ($value_compare_func) {
			$result = call_user_func($value_compare_func, $a[1], $b[1]);
			return $result === 0 ? $a[0] - $b[0] : $result;
		});
		foreach ($array as &$item) {
			$item = $item[1];
		}
		return $result;
	}
}