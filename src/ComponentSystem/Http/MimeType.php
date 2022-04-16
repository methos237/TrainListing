<?php

namespace Http;

use Util\Enum;

/**
 * A MIME type definition
 *
 * @method static MimeType JSON
 * @author James Polk
 */
final class MimeType extends Enum {
	
	public const JSON = ["application", "json"];
	
	protected string $subtype;
	protected string $type;
	protected array $parameters;
	
	/**
	 * Create a new MIME type
	 * @param string $type - The MIME type(i.e. application or text)
	 * @param string $subtype - The MIME subtype(i.e. json or html)
	 * @param array $parameters - An associative array of additional parameters for the
	 *                              type(i.e. charset=>"UTF-8")
	 */
	private function __construct(string $type, string $subtype, array $parameters = []) {
		$this->type = $type;
		$this->subtype = $subtype;
		$this->parameters = $parameters;
	}
	
	public function getType(): string {
		return $this->type;
	}
	
	public function getSubtype(): string {
		return $this->subtype;
	}
	
	public function getParameters(): array {
		return $this->parameters;
	}
	
	public static function parse(string $mimeString): MimeType {
		//TODO: Implement this
	}
	
	public function __toString(): string {
		$mimeString = "$this->type/$this->subtype";
		if (!empty($this->parameters)) {
			$mimeString .= ";" . implode(";", array_map(function ($key, $value) {
					return "$key=$value";
				}, $this->parameters));
		}
		return $mimeString;
	}
	
}