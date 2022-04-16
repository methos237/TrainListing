<?php

namespace Util;

class UrlUtil {
	
	private const GZ_COMPRESSION_LEVEL = 9;
	
	public const PATH_SEPARATOR = "/";
	
	public const MODE_URL_ENCODE = 4;
	
	/**
	 * Escape a URL(or URL component) for use in HTML links
	 *
	 * @param String $value - The value to escape
	 * @return String - A URL encoded appropriately for use in HTML links
	 */
	public static function escape(string $value): string {
		return htmlspecialchars(urlencode($value));
	}
	
	/**
	 * Compress a string and encode in base 64(for use in a URL) to reduce space and obfuscate(neither securely encrypt
	 * nor obfuscate in a cryptographically secure way, in other words, this is for appearance not security) values
	 *
	 * @param String|null $string - The string to be compressed
	 * @return String - The compressed string
	 */
	public static function compress(?string $string): string {
		return base64_encode(gzdeflate($string, self::GZ_COMPRESSION_LEVEL));
	}
	
	/**
	 * Decompress/decode a string as compressed/encoded by URLUtil::compress
	 *
	 * @param String|null $string - The compressed string
	 * @return String - The uncompressed string(exactly as passed to URLUtil::compress) or an empty string if $string
	 *     is null or blank
	 */
	public static function decompress(?string $string): string {
		if ($string === null || $string === "") {
			return "";
		}
		return gzinflate(base64_decode(str_replace(' ', '+', $string)));
	}
	
	/**
	 * Assemble the path portion of a URL from the given components
	 *
	 * @param $mode - Bitmask of mode options(@see UrlUtil::MODE_URL_ENCODE, @see PathUtil::assemblePath)
	 * @param string ...$components - The set of components from which to build the path
	 * Example:
	 *    "/$components[0]/$components[1]/$components[n]...
	 * @return string
	 * @see PathUtil::assemblePath
	 */
	public static function assemblePath($mode, ...$components): string {
		if (!is_bool($mode) && ($mode & self::MODE_URL_ENCODE) === self::MODE_URL_ENCODE) {
			$components = array_map("rawurlencode", $components);
		}
		return PathUtil::assemblePath($mode, ...$components);
	}
	
}