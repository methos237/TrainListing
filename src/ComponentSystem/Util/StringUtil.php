<?php

namespace Util;

use Exception;

/**
 * StringUtil
 * Contains string utility functions
 * @author James Polk
 */
class StringUtil {
	
	public const QUOTE = '"';
	public const DEFAULT_PADDING = "0";
	
	//******WARNING******
	//PHP's strlen function returns the number of bytes in a string, rather than the number of characters.
	//In ASCII, this is not a problem as the two values are equivalent(1 character=1 byte).
	//With Unicode(and other encodings with multiple byte characters), this is problematic as the number of bytes will not necessarily be equal to the number of characters.
	//Several functions in StringUtil make use of strlen:
	// * StringUtil::startsWith
	// * StringUtil::endsWith
	//This should not be an issue with typical usage, but if working with Unicode strings, BE CAREFUL with this.
	//*******************
	
	/**
	 * Add a space after any occurrence of a given substring that is not already followed by a space
	 *
	 * @param string $after Character(s) to insert a space after
	 * @param string $subject String to search within
	 * @return string
	 */
	public static function addSpaceAfter(string $after, string $subject): string {
		// Match any occurrence of $after not immediately followed by a space
		$pattern = '/(' . preg_quote($after) . ')([^ ])/';
		// Replace with $after, a new space, and the original subsequent character
		$replacement = '${1} ${2}';
		
		return preg_replace($pattern, $replacement, $subject);
	}
	
	/**
	 * Polyfill for str_contains() in PHP < 8.0
	 * Based on Larvel framework
	 * TODO: This can be removed and refactored after adopting PHP 8.0
	 * @param string $haystack
	 * @param string $needle
	 * @return bool
	 */
	public static function str_contains(string $haystack, string $needle): bool {
		if (!function_exists('str_contains')) {
			return $needle !== '' && mb_strpos($haystack, $needle) !== false;
		}
		return str_contains($haystack, $needle);
	}
	
	
	/**
	 * Check if a string begins with the given substring
	 * @param string $str - The string to test
	 * @param string $val - The substring to look for in $str
	 * @return boolean - Whether or not $str begins with $val
	 */
	public static function startsWith(string $str, string $val): bool {
		$findLength = strlen($val);
		if ($findLength > strlen($str)) {
			return false;
		}
		if (strpos($str, $val) === 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * Check if a string ends with the given substring
	 * @param string $str - The string to test
	 * @param string $val - The substring to look for in $str
	 * @return boolean - Whether $str ends with $val
	 */
	public static function endsWith(string $str, string $val): bool {
		$findLength = strlen($val);
		if ($findLength > strlen($str)) {
			return false;
		}
		if (substr($str, -$findLength) === $val) {
			return true;
		}
		return false;
	}
	
	/**
	 * Compare two strings disregarding case
	 * @return bool - Whether the strings are equal
	 */
	public static function equalsIgnoreCase(string $a, string $b): bool {
		return strtolower($a) === strtolower($b);
	}
	
	/**
	 * Surrounds the value of the given string with quotes(inside the string)
	 * @param string $str - The string to quote
	 * @return string - The string surrounded with quotes
	 */
	public static function quote(string $str): string {
		return self::QUOTE . $str . self::QUOTE;
	}
	
	/**
	 * Limit the length of a string by truncating extra characters
	 *
	 * If the optional $padding parameter is specified and not null, then the string will be padded(appened to the end of the string) with $padding for each character the string is short of the length. After adding padding,
	 * $str will be run through StringUtil::limitLength again, but without the padding parameter, in order to ensure that it still no longer than the specified length. It could exceed that length if $padding were longer
	 * than a single character.
	 *
	 * @param string $str - The string to limit
	 * @param int $len - The max length of the string
	 * @param string|null $padding - [Optional, Defaults to null] - Text(typically a single character, though any string work) to use for each character $str is shorter than the $len
	 * @return string - The first $len characters of $str or $str if the length of $str is less than $len already
	 */
	public static function limitLength(string $str, int $len, string $padding = null): string {
		$diff = strlen($str) - $len;
		if ($diff > 0) {
			return substr($str, 0, $len);
		}
		if ($padding === null) {
			return $str;
		}
		for ($i = $diff; $i < 0; $i++) {
			$str .= $padding;
		}
		return self::limitLength($str, $len); //Make sure length is still limited after padding is added(padding could be a multi-character string and thus cause the length of $str to exceed $len)
	}
	
	/**
	 * Convert a string from camel case to lowercase with underscores as the delimiter
	 * Ex: thisIsCamelCase => this_is_camel_case
	 * @param string $camelCase - The camel case string
	 * @return string - The lowercase/underscore-delimited string
	 */
	public static function camelCaseToLowerUnderscore(string $camelCase): string {
		return strtolower(preg_replace("/(.)([A-Z])/", "$1_\$2", $camelCase));
	}
	
	/**
	 * Get all indices of the substring in the given string
	 * @param string $string - The string in which to search
	 * @param string $subString - The substring to search for
	 * @return array - An array containing each index at which the character was found
	 */
	public static function getSubstringPositions(string $string, string $subString): array {
		$indices = [];
		$offset = 0;
		while (($index = strpos($string, $subString, $offset)) !== false) {
			$offset = $index + 1;
			$indices[] = $offset;
		}
		return $indices;
	}
	
	/**
	 * Get a substring based on the start and end indices
	 * @param string $str - The string on which to operate
	 * @param int $start - The start index
	 * @param int|null $end - The end index [Optional, if omitted the remainded of the string will be included]
	 * @return string - The substring or null if out of range
	 */
	public static function subStringByIndex(string $str, int $start, int $end = null): ?string {
		if (($end !== null && $end < $start) || $start < 0) {
			return null;
		} //TODO: Throw exception here(something like Java's StringIndexOutOfBoundsException)
		$substr = ($end === null ? substr($str, $start) : substr($str, $start, $end - $start));
		if ($substr === false) {
			return null;
		} //TODO: Throw exception here
		return $substr;
	}
	
	/**
	 * Pad the given string with additional characters to match the specified length
	 * @param string $str - The string to pad
	 * @param int $length - The length to which to pad the string
	 * @param bool $trunc - If true, truncate the string if it exceeds length
	 * @param string $padding - The string to use for padding(generally a single character); Default: self::DEFAULT_PADDING
	 * @return string - The padded string
	 */
	public static function padLeft(string $str, int $length, bool $trunc = false, string $padding = self::DEFAULT_PADDING): string {
		while (strlen($str) < $length) {
			$str = $padding . $str;
		}
		return $trunc ? self::limitLength($str, $length) : $str;
	}
	
	/**
	 * Join strings with the given delimiter. Empty strings will be ignored so that there will be
	 * no adjacent occurrences of delimiters.
	 * @param string $delimiter - The delimiter to use
	 * @param string ...$strings - The strings to join
	 */
	public static function join(string $delimiter, ?string ...$strings): string {
		$joined = "";
		$first = true;
		foreach ($strings as $string) {
			if (!empty($string)) {
				if (!$first)
					$joined .= $delimiter;
				$joined .= $string;
				$first = false;
			}
		}
		return $joined;
	}
	
	/**
	 * Remove the specified values from a string
	 * @param string $str - The string on which to operate
	 * @param array $characters - An array of characters(or strings) to remove
	 * @return string - The string with the specified characters removed
	 */
	public static function remove(string $str, array $characters): string {
		return str_replace($characters, "", $str);
	}
	
	/**
	 * Generate a random string of the specified length using the given alphabet and a CSPRNG
	 * @param int $length - The length of the output string
	 * @param string|null $alphabet - A string containing characters to be used in the output string or null to use any ASCII characters
	 * @throws Exception
	 */
	public static function randomString(int $length, string $alphabet = null): string {
		$out = "";
		$max = $alphabet !== null ? (strlen($alphabet) - 1) : 255;
		for (; $length > 0; $length--) {
			$out .= $alphabet !== null ? $alphabet[random_int(0, $max)] : chr(random_int(0, $max));
		}
		return $out;
	}
	
}