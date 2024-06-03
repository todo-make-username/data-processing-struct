<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Reflector;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

class ObjectReflector
{
	/**
	 * The reflection helper for a specified object.
	 *
	 * @phan-var ReflectionClass<object>
	 */
	private ReflectionClass $ObjectReflector;

	/**
	 * The constructor
	 *
	 * @template T of object
	 * @phan-param ReflectionClass<T> $Object
	 *
	 * @param ReflectionClass $Object The object that needs processed.
	 */
	public function __construct(ReflectionClass $Object)
	{
		$this->ObjectReflector = $Object;
	}

	/**
	 * Get the object's properties.
	 *
	 * @param integer $flags ReflectionProperty const.
	 * @return ReflectionProperty[]
	 */
	public function getObjectProperties(int $flags=ReflectionProperty::IS_PUBLIC): array
	{
		return $this->ObjectReflector->getProperties($flags);
	}

	/**
	 * Get attributes on the object.
	 *
	 * @template T as object
	 * @phan-param class-string<T>|null $filter_class Name of an attribute class to filter by.
	 * @phan-return ReflectionAttribute<T>[]
	 *
	 * @param string|null $filter_class Name of an attribute class to filter by.
	 * @return ReflectionAttribute[]
	 */
	public function getAllObjectAttributes(?string $filter_class=null): array
	{
		return $this->ObjectReflector->getAttributes($filter_class, ReflectionAttribute::IS_INSTANCEOF);
	}

	/**
	 * Get the specified attribute from the object if it exits.
	 *
	 * @template T as object
	 * @phan-param class-string<T> $attribute_name The name of the attribute.
	 * @phan-return ReflectionAttribute<T>|null
	 *
	 * @param string $attribute_name The name of the attribute.
	 * @return ReflectionAttribute|null
	 */
	public function getObjectAttribute(string $attribute_name): ?ReflectionAttribute
	{
		$Attributes = $this->getAllObjectAttributes($attribute_name);
		$Attribute  = (count($Attributes) > 0) ? $Attributes[0] : null;

		return $Attribute;
	}

}