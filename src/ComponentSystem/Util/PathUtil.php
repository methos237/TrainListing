<?php

namespace Util;

/**
 * A set of utilities related to paths (for files, URLs, etc.)
 * @author James Polk
 */
class PathUtil {
	
	public const PATH_SEPARATOR = "/";
	public const ALTERNATE_PATH_SEPARATOR = "\\";
	public const EXTENSION_SEPARATOR = ".";
	
	public const MODE_TRAILING_SLASH = 1;
	public const MODE_PRECEDING_SLASH = 2;
	
	/**
	 * Assemble a path from the given components
	 * @param $separatorMode - A bitmask defining options for Preceding and trailing slashes.
	 *                           For legacy reason, if true, path will end with '/'
	 * @param string ...$components - The set of components from which to build the path
	 * Example:
	 *    "/$components[0]/$components[1]/$components[n]...
	 * TODO: Don't assume that all paths are absolute...
	 */
	public static function assemblePath($separatorMode, ...$components): string {
		//Allow bool params for legacy reasons and maintain same behavior
		if (is_bool($separatorMode)) {
			if ($separatorMode === true) {
				$separatorMode = self::MODE_TRAILING_SLASH;
			}
			$separatorMode |= self::MODE_PRECEDING_SLASH;
		}
		$path = "";
		if (($separatorMode & self::MODE_PRECEDING_SLASH) === self::MODE_PRECEDING_SLASH) {
			$path = self::PATH_SEPARATOR;
		}
		$endWithSeparator = (($separatorMode & self::MODE_TRAILING_SLASH) === self::MODE_TRAILING_SLASH);
		$p = 0;
		$end = count($components) - 1;
		foreach ($components as $component) {
			if (StringUtil::startsWith($component, self::PATH_SEPARATOR)) {
				$component = substr($component, 1);
			}
			if (StringUtil::endsWith($component, self::PATH_SEPARATOR)) {
				$component = substr($component, 0, -1);
			}
			$path .= $component;
			if ($p < $end || $endWithSeparator) {
				$path .= self::PATH_SEPARATOR;
			}
			$p++;
		}
		return $path;
	}
	
	/**
	 * Add an extension to a file name
	 * @param string $name - The file name
	 * @param string|null $extension - The file extension(if null, the original name will be returned)
	 * @return string - The file name with the extension added
	 * Example:
	 *     $name="test"; $extension="json"; return: "test.json"
	 */
	public static function addExtension(string $name, ?string $extension): string {
		if ($extension === null) {
			return $name;
		}
		return $name . self::EXTENSION_SEPARATOR . $extension;
	}
	
	/**
	 * Remove the extension from a file name
	 * @param string $name - The file name(not full path)
	 * @param &$extension - The extension will be written to this value once removed
	 * @return string - The name of the file without an extension
	 */
	public static function removeExtension(string $name, &$extension): string {
		$components = explode(self::EXTENSION_SEPARATOR, $name, 2);
		$extension = $components[1] ?? "";
		return $components[0];
	}
	
	/**
	 * Get the extension of this file
	 * @param $name - the file name
	 * @return string|null - the extension(not including the separator) or null if there is no extension
	 */
	public static function getExtension(string $name): ?string {
		$components = explode(self::EXTENSION_SEPARATOR, $name, 2);
		if (count($components) === 2) {
			return $components[1];
		}
		return null;
	}
	
	/**
	 * Append a string to the end of a file name before the extension
	 * @param string $name - The file name(not full path)
	 * @param string $append - The value to be appended
	 * @return string - The new filename
	 * Example:
	 *    $name="test.json", $append="-01"
	 *    result: "test-01.json"
	 */
	public static function appendName(string $name, string $append): string {
		$extension = null;
		$originalName = self::removeExtension($name, $extension);
		return self::addExtension($originalName . $append, $extension);
	}
	
	/**
	 * Split the components of a path
	 * @param string $path - The path to split
	 * @return array - The components of the path
	 */
	public static function splitComponents(string $path): array {
		return array_values(array_filter(explode(self::PATH_SEPARATOR, $path), static function ($value) {
			return $value !== "";
		}));
	}
	
	/**
	 * Get the last component of a path
	 * @param string $path - The path
	 * @return string|null - The last component of the path or null if the path has no components
	 */
	public static function getLastComponent(string $path): ?string {
		$components = self::splitComponents($path);
		$count = count($components);
		if ($count > 0) {
			return $components[$count - 1];
		}
		return null;
	}
	
	/**
	 * Check if a path is absolute
	 * @return bool - True if the path is absolute, false if the path is relative
	 */
	public static function isAbsolute(string $path): bool {
		return StringUtil::startsWith($path, self::PATH_SEPARATOR);
	}
	
	/**
	 * Check if a given path ends with a slash
	 * @param string $path - The path
	 * @return bool - True if the path ends with a slash, false otherwise
	 */
	public static function hasTrailingSlash(string $path): bool {
		return StringUtil::endsWith($path, self::PATH_SEPARATOR);
	}
	
	/**
	 * Remove any path separators(i.e. /) from the path
	 * @param string $path - The path to filter
	 * @param string $replacements - The value with which to replace path separators
	 * @return string - The filtered path
	 */
	public static function removeSeparators(string $path, string $replacement = ""): string {
		return str_replace([self::PATH_SEPARATOR, self::ALTERNATE_PATH_SEPARATOR], $replacement, $path);
	}
	
}