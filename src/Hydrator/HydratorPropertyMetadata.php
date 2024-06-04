<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Hydrator;

use ReflectionProperty;

class HydratorPropertyMetadata
{
	/**
	 * The Reflection object for this property.
	 */
	public readonly ReflectionProperty $Property;

	/**
	 * If the array used to hydrate the object contained a value for this property.
	 *
	 * PHP's array_key_exists was used on the array to determine this value.
	 */
	public readonly bool $value_exists;

	/**
	 * The initial value that was provided in the hydration array. Null if **$value_exists** is **FALSE**.
	 */
	public readonly mixed $preprocessed_value;

	/**
	 * If this property should be hydrated. [Default: **TRUE**]
	 *
	 * Overridden from the HydrationSettings attribute on either the class and/or the specific object's property. The property attribute will overwrite the class attribute values.
	 */
	public readonly bool $hydrate;

	/**
	 * If type conversion is enabled for this property. [Default: **TRUE**]
	 *
	 * Overridden from the HydrationSettings attribute on either the class and/or the specific object's property. The property attribute will overwrite the class attribute values.
	 */
	public readonly bool $convert;


	/**
	 * The Class Constructor
	 *
	 * @param ReflectionProperty $Property           The value to assign to the Property readonly property.
	 * @param boolean            $value_exists       The value to assign to the value_exists readonly property.
	 * @param mixed              $preprocessed_value The value to assign to the preprocessed_value readonly property.
	 * @param boolean            $hydrate            The value to assign to the hydrate readonly property.
	 * @param boolean            $convert            The value to assign to the convert readonly property.
	 */
	public function __construct(
		ReflectionProperty $Property,
		bool $value_exists,
		mixed $preprocessed_value,
		bool $hydrate,
		bool $convert,
	)
	{
		$this->Property           = $Property;
		$this->value_exists       = $value_exists;
		$this->preprocessed_value = $preprocessed_value;
		$this->hydrate            = $hydrate;
		$this->convert            = $convert;
	}
}