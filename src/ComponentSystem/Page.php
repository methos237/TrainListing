<?php

namespace ComponentSystem;

use RuntimeException;

/**
 * This builds an entire page made up of containers and components for display
 */
class Page extends Container {
	
	private ?string $title;
	private ?string $metaDescription;
	private ?string $keywords;
	private ?string $canonicalUrl;
	private array $components;
    
    protected ?Component $header = null;
    protected ?Component $footer = null;
	
	/**
	 * Creates a new Page
	 * @param string|null $title
	 * @param string|null $metaDescription
	 * @param string|null $keywords
	 * @param string|null $canonicalUrl
	 * @param array $components
	 */
	public function __construct(?string $title = null, ?string $metaDescription = null, ?string $keywords = null, ?string $canonicalUrl = null, Component ...$components) {
		$this->title = $title;
		$this->metaDescription = $metaDescription;
		$this->keywords = $keywords;
		$this->canonicalUrl = $canonicalUrl;
		$this->components = $components;
	}
	
	/**
	 * @param Component $header
	 * @return Page
	 */
	public function setHeader(Component $header): self {
		$this->header = $header;
        return $this;
	}
	
	/**
	 * @param Component $footer
	 * @return Page
	 */
	public function setFooter(Component $footer): self {
		$this->footer = $footer;
        return $this;
	}
 
 
	
	/**
	 * Adds the header and footer components to the page. {@inheritDoc}
	 */
	public function getComponents(): array {
		$components = $this->components;
        if ($this->header !== null) {
	        array_unshift($components, ($this->header));
        }
		if ($this->footer !== null) {
			$components[] = ($this->footer);
        }
		return $components;
	}
	
	/**
	 * Builds the document head of the webpage. {@inheritDoc}
	 */
	public function display(): void { ?>
		<!doctype html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<meta http-equiv="X-UA-Compatible" content="ie=edge">
			<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1">
			<meta name="description" content="<?= htmlspecialchars($this->metaDescription) ?>">
			<?php if($this->keywords !== null): ?>
				<meta name="keywords" content="<?= htmlspecialchars($this->keywords) ?>">
			<?php endif; ?>
			<title><?= htmlspecialchars($this->title) ?></title>
			<?php if($this->canonicalUrl !== null): ?>
				<link rel="canonical" href="<?= htmlspecialchars($this->canonicalUrl) ?>">
			<?php endif; ?>
			<?= self::resolveDependencies($this->getDependencies()); ?>
		</head>
		<body>
		<?php parent::display(); ?>
		</body>
		</html>
	<?php
	}
	
	/**
	 * Resolves all dependencies to the correct header encoding
	 * @param array $dependencies
	 * @return string - string of all needed dependencies, correctly encoded
	 */
	private static function resolveDependencies(array $dependencies): string {
		// Attack the dependency clones
		$dependencies = array_unique($dependencies);
		$output = "";
		foreach ($dependencies as $dependency) {
			if (strpos($dependency, "/") === 0) {
				$dependency = substr($dependency, 1);
				$extension = pathinfo($dependency, PATHINFO_EXTENSION);
				switch (strtolower($extension)) {
					case "css":
						$dependency = "<link rel='stylesheet' type='text/css href='/" . htmlspecialchars($dependency) ."'>";
						break;
					case "js":
						$dependency = "<script type='text/javascript' src='/". htmlspecialchars($dependency) . "' defer></script>";
						break;
					default:
						throw new RuntimeException("Encountered an unrecognized dependency type: $dependency");
				}
			}
			$output .= $dependency;
		}
		return $output;
	}
	
}