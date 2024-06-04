<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Validator;

use ReflectionProperty;

class ValidatorPropertyMetadata
{
	/**
	 * The Reflection object for this property.
	 */
	public readonly ReflectionProperty $Property;

	/**
	 * Determines if the object's property has been initialized or not.
	 *
	 * This will ALWAYS be true for non-typed properties. Blame ReflectionProperty not me.
	 */
	public readonly bool $is_initialized;

	/**
	 * The Class Constructor
	 *
	 * @param ReflectionProperty $Property       The value to assign to the Property readonly property.
	 * @param boolean            $is_initialized The value to assign to the is_initialized readonly property.
	 */
	public function __construct(
		ReflectionProperty $Property,
		bool $is_initialized,
	)
	{
		$this->Property       = $Property;
		$this->is_initialized = $is_initialized;
	}
}