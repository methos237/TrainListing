<?php

namespace Util;

/**
 * A component of a path
 * @author James Polk
 */
class PathComponent {
	
	protected string $value;
	protected bool $precedingSlash;
	protected bool $trailingSlash;
	
	public function __construct(string $value, bool $trailingSlash = false, bool $precedingSlash = false) {
		$this->value = $value;
		$this->trailingSlash = $trailingSlash;
		$this->precedingSlash = $precedingSlash;
	}
	
	public function getValue(): string {
		return $this->value;
	}
	
	public function setTrailingSlash(bool $trailingSlash): self {
		$this->trailingSlash = $trailingSlash;
		return $this;
	}
	
	public function hasTrailingSlash(): bool {
		return $this->trailingSlash;
	}
	
	public function setPrecedingSlash(bool $precedingSlash): self {
		$this->precedingSlash = $precedingSlash;
		return $this;
	}
	
	public function hasPrecedingSlash(): bool {
		return $this->precedingSlash;
	}
	
	public function __toString() {
		return $this->value;
	}
	
}