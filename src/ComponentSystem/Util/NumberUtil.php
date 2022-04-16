<?php

namespace Util;

/**
 * A set of utilities related to numbers
 * @author James Polk
 */
class NumberUtil {
	
	public const PRICE_DECIMALS = 2;
	public const PADDING_DIGIT = "0";
	
	public const ALPHABET_DEFAULT = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"; //Used for base conversion. 0=0, 16=G, 35=Z, etc. A map for converting numbers to characters.
	public const ALPHABET_HEX = "0123456789abcdef";
	public const ALPHABET_BASE62 = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	
	private const BITS_PER_BYTE = 8;
	
	public static function roundPrice($price): float {
		return round($price, self::PRICE_DECIMALS);
	}
	
	public static function formatPrice($price, $includeUnit = true): string {
		return ($includeUnit ? "$" : "") . number_format(self::roundPrice($price), self::PRICE_DECIMALS);
	}
	
	public static function formatCost(float $cost): string {
		if ($cost === 0.0) {
			return "FREE";
		}
		return self::formatPrice($cost);
	}
	
	public static function formatDiscount(float $amount): string {
		return ($amount > 0 ? "-" : "") . self::formatPrice($amount);
	}
	
	/**
	 * Get a string representing a floating point price to the nearest real currency unit(i.e. 1 cent)
	 * @param $price - A floating point representation of the price
	 * @return string - An exact string representation of the price appropriate for comparisons
	 */
	public static function convertPrice(float $price): string {
		return number_format(self::roundPrice($price), self::PRICE_DECIMALS, '.', '');
	}
	
	/**
	 * Compare two floating point prices for equality
	 * @param $a - The first price
	 * @param $b - The second price
	 * @return true if the prices match to the penny(0.01), false otherwise
	 */
	public static function comparePrices(float $a, float $b): bool {
		return self::convertPrice($a) === self::convertPrice($b);
	}
	
	public static function formatUnits($number, $singularUnit, $pluralUnit = null, $unitDivider = " "): string {
		return number_format($number) . $unitDivider . ($number === 1 ? $singularUnit : ($pluralUnit === null ? $singularUnit . "s" : $pluralUnit));
	}
	
	/**
	 * Format a decimal number as a percent
	 * @param double $decimal - The number to format
	 * @return string - $decimal formatted as a percent for display
	 */
	public static function formatPercent(float $decimal, $places = 0): string {
		return number_format(round($decimal * 100.0, $places)) . "%";
	}
	
	/**
	 * Format a number using the specified mask
	 * The mask is a string wherein number signs will be replaced with the digits of the number
	 * @param mixed $number - The number to format
	 * @param string $mask - The mask to use(Ex: (###)-###-#### for a US phone number)
	 * @param bool $truncateMask - If true, mask will be truncated after last # if the mask has more placeholders than the length of number(as a string)
	 * @return string - The formatted number
	 * TODO: Add support for escaping number signs
	 */
	public static function format($number, $mask, $truncateMask = false, $allowLetters = false): string {
		if (!is_string($number))
			$number = (string)$number;
		$number = preg_replace($allowLetters ? "/[^\dA-z]/" : "/[^\d]/", "", $number);
		$numLen = strlen($number);
		if ($allowLetters) {
			$mask = str_replace("!", "#", $mask);
		}
		$maskLen = substr_count($mask, "#");
		if ($numLen < $maskLen && $truncateMask) {
			$formatted = substr($mask, 0, StringUtil::getSubstringPositions($mask, "#")[max($numLen - 1, 0)]);
		} else {
			$formatted = $mask;
		}
		for ($i = 0; $i < $numLen; $i++) {
			$formatted = preg_replace("/#/", $number[$i], $formatted, 1);
		}
		return preg_replace("/#/", "", $formatted);
	}
	
	
	/**
	 * Get all only digit characters from a string
	 * (Remove non-digit characters)
	 * @param string $str - The string from which to extract digits
	 * @return string - $str with all non-digit characters removed
	 */
	public static function getDigits($str) {
		return preg_replace("/[\D]/", "", $str);
	}
	
	/**
	 * Parse a string as an integer by removing all non-digit characters from the string
	 * @param string $str - The string to parse
	 * @return int
	 */
	public static function parseInt($str) {
		return (int)self::getDigits($str);
	}
	
	/**
	 * Converts a number from base 10 to an arbitrary base
	 * @param string $num - The base 10 number to convert
	 * @param int $base - The base to which to convert $num
	 * @param string|null $alphabet - [Optional; Defaults to NumberUtil::ALPHABET_DEFAULT] The characters to use in order of value for the numbers (@see NumberUtil::ALPHABET_DEFAULT)
	 * NOTE: strlen($alphabet) must be >= $base
	 * If null is provided, the full range of ASCII characters will be used
	 * @return string - $num represented in base $base
	 */
	public static function toBaseCustom(string $num, int $base = 10, ?string $alphabet = self::ALPHABET_DEFAULT): string {
		$converted = "";
		while ($num > 0) {
			$mod = bcmod($num, $base, 0);
			$converted = ($alphabet === null ? chr($mod) : $alphabet[$mod]) . $converted;
			$num = bcdiv($num, $base, 0);
		}
		return $converted;
	}
	
	/**
	 * Converts a number from an arbitrary base to base 10
	 * @param string $num - A number in base $base to convert
	 * @param int $base - The base from which to convert
	 * @param string $alphabet - [Optional; Defaults to NumberUtil::ALPHABET_DEFAULT] The characters to use in order of value for the numbers (@see NumberUtil::ALPHABET_DEFAULT)
	 * NOTE: $alphabet should be the same alphabet used in toBaseCustom (@return string - $num represented in base 10
	 * @see NumberUtil::toBaseCustom)
	 * NOTE: strlen($alphabet) must be >= $base
	 * If null is provided, the full range of ASCII characters will be used
	 */
	public static function fromBaseCustom(string $num, int $base = 10, string $alphabet = self::ALPHABET_DEFAULT): string {
		$converted = "0";
		$len = strlen($num);
		for ($i = 0; $i < $len; $i++) {
			$strI = $len - $i - 1;
			$converted = bcadd($converted, bcmul(bcpow($base, $i, 0), $alphabet === null ? ord($num[$strI]) : strpos($alphabet, $num[$strI]), 0));
		}
		return $converted;
	}
	
	/**
	 * Converts a number from base 10 to the maximum base possible using the specified alphabet
	 * @param int $num - A base 10 number to convert
	 * @param string $alphabet - [Optional; Defaults to NumberUtil::ALPHABET_DEFAULT] The characters to use in order of value for the numbers (@see NumberUtil::ALPHABET_DEFAULT)
	 * @return string - $num represented in the highest possible base allowed with $alphabet
	 */
	public static function toMaxBaseCustom(int $num, string $alphabet = self::ALPHABET_DEFAULT) {
		return self::toBaseCustom($num, $alphabet === null ? 255 : strlen($alphabet), $alphabet);
	}
	
	/**
	 * Converts a number from the maximum base possible using $alphabet to base 10
	 * @param string $num - A number in the maximum base possible with $alphabet(typically output from NumberUtil::toMaxBaseCustom, @see NumberUtil::toMaxBaseCustom)
	 * @param string $alphabet - [Optional; Defaults to NumberUtil::ALPHABET_DEFAULT] The characters to use in order of value for the numbers (@see NumberUtil::ALPHABET_DEFAULT)
	 * @return int - $num represented in base 10
	 */
	public static function fromMaxBaseCustom(string $num, string $alphabet = self::ALPHABET_DEFAULT) {
		return self::fromBaseCustom($num, $alphabet === null ? 255 : strlen($alphabet), $alphabet);
	}
	
	/**
	 * Convert a number between bases using the maximum possible base allowed by each alphabet
	 * @param string $num - the number to convert
	 * @param string|null $fromAlphabet - the alphabet used for the current representation of $num
	 * @param string|null $toAlphabet - the alphabet to which to convert the number
	 * @return string - the converted number
	 */
	public static function convertMaxBaseCustom(string $num, ?string $fromAlphabet, ?string $toAlphabet): string {
		return self::toMaxBaseCustom(self::fromMaxBaseCustom($num, $fromAlphabet), $toAlphabet);
	}
	
	/**
	 * Returns the sum of the digits of a number
	 * @param int $num - A number
	 * @param int $base - [Optional; Defaults to 10] The base for the number
	 * @param string $alphabet - [Optional; Defaults to NumberUtil::ALPHABET_DEFAULT] The alphabet to use for base conversion (@see NumberUtil::ALPHABET_DEFAULT)
	 * @return int - The sum of the digits that compose $num
	 */
	public static function digitSum(int $num, int $base = 10, string $alphabet = self::ALPHABET_DEFAULT) {
		$num = (string)$num;
		$sum = 0;
		for ($i = 0; $i < strlen($num); $i++) {
			$sum += self::fromBaseCustom($num[$i], $base, $alphabet);
		}
		return $sum;
	}
	
	/**
	 * Convert a number to a zero-padded string of a fixed length
	 * @param int $num - The number
	 * @param int $digits - The number of digits the resulting string should contain
	 * @param bool $trunc - If true, truncate the string if it exceeds the specified number of digits; Default: false
	 */
	public static function padDigits(int $num, int $digits, bool $trunc = false): string {
		$str = (string)$num;
		$padded = StringUtil::limitLength($str, $digits);
		while (strlen($str) < $digits) {
			$str = self::PADDING_DIGIT . $str;
		}
		return $str;
	}
	
	/**
	 * Get the number of digits in a number
	 * @param int $num - The number
	 * @param int $base - The base to use for determining the number of digits [Default: 10]
	 * @param string $alphabet - The alphabet to use for base conversion [Default: NumberUtil::ALPHABET_DEFAULT]
	 * @return int - The number of digits in the number
	 */
	public static function getDigitCount(int $num, int $base = 10, string $alphabet = self::ALPHABET_DEFAULT): int {
		$converted = self::toBaseCustom($num, $base, $alphabet);
		return strlen((string)$converted);
	}
	
}