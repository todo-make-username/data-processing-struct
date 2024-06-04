<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Tailor\Abstracts;

use ReflectionProperty;
use TodoMakeUsername\DataProcessingStruct\Attributes\Shared\DataProcessingAttributeInterface;

abstract class AbstractTailorAttribute implements DataProcessingAttributeInterface
{
	/**
	 * The reflection object of the property this attribute is on.
	 */
	public ReflectionProperty $Property;

	/**
	 * Determines if the object property was initialized with a value or not.
	 *
	 * This will ALWAYS be true for non-typed properties. Blame ReflectionProperty not me.
	 */
	public bool $is_initialized;

	/**
	 * Process the value and return any changes.
	 *
	 * @param mixed $value The value to process.
	 * @return mixed Returns the processed value.
	 */
	abstract public function process(mixed $value): mixed;
}