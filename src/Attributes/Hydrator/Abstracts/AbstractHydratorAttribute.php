<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Abstracts;

use ReflectionProperty;

abstract class AbstractHydratorAttribute
{
	/**
	 * The reflection object of the property this attribute is on.
	 */
	public ReflectionProperty $Property;

	/**
	 * If the data was passed in with the hydration data or not.
	 */
	public bool $value_exists;

	/**
	 * Process the value before hydration.
	 *
	 * @param mixed $value The value to process.
	 * @return mixed Returns the processed value.
	 */
	abstract public function process(mixed $value): mixed;
}