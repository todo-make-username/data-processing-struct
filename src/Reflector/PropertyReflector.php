<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Reflector;

use ReflectionAttribute;
use ReflectionProperty;

class PropertyReflector
{
	/**
	 * The reflection property.
	 */
	private ReflectionProperty $PropertyReflector;

	/**
	 * The constructor
	 *
	 * @param ReflectionProperty $Property The reflection of the property.
	 */
	public function __construct(ReflectionProperty $Property)
	{
		$this->PropertyReflector = $Property;
	}

	/**
	 * Get attributes on the property.
	 *
	 * @template T as object
	 * @phan-param class-string<T>|null $filter_class
	 * @phan-return ReflectionAttribute<T>[]
	 *
	 * @param string|null $filter_class Name of an attribute class to filter by.
	 * @return ReflectionAttribute[]
	 */
	public function getAllPropertyAttributes(?string $filter_class=null): array
	{
		return $this->PropertyReflector->getAttributes($filter_class, ReflectionAttribute::IS_INSTANCEOF);
	}

	/**
	 * Get the specified attribute from the property if it exits.
	 *
	 * @template T as object
	 * @phan-param class-string<T> $attribute_name
	 *
	 * @param string $attribute_name The name of the attribute.
	 * @return ReflectionAttribute<T>|null
	 */
	public function getPropertyAttribute(string $attribute_name): ?ReflectionAttribute
	{
		$Attributes = $this->getAllPropertyAttributes($attribute_name);
		$Attribute  = (count($Attributes) > 0) ? $Attributes[0] : null;

		return $Attribute;
	}
}