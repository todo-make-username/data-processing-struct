<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Tailor;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use TodoMakeUsername\DataProcessingStruct\Attributes\Shared\DataProcessingAttributeInterface;
use TodoMakeUsername\DataProcessingStruct\Attributes\Tailor\Abstracts\AbstractTailorAttribute;
use TodoMakeUsername\DataProcessingStruct\Reflector\ObjectReflector;

class TailorHelper
{
	/**
	 * Run any tailor attributes on the desired object.
	 *
	 * @param object $Object The object to process tailor attributes on.
	 * @return void
	 */
	public function tailorObject(object $Object): void
	{
		$ObjectReflector = new ObjectReflector(new ReflectionClass($Object));

		$Properties = $ObjectReflector->getObjectProperties(ReflectionProperty::IS_PUBLIC);
		foreach ($Properties as $Property)
		{
			$this->tailorObjectProperty($Object, $Property);
		}
	}

	/**
	 * Tailor an object's property.
	 *
	 * @param object             $Object   The object with the property to tailor.
	 * @param ReflectionProperty $Property The property Reflection object.
	 * @return void
	 */
	protected function tailorObjectProperty(object $Object, ReflectionProperty $Property): void
	{
		$property_name  = $Property->name;
		$is_initialized = $Property->isInitialized($Object);
		$initial_value  = ($is_initialized) ? $Object->{$property_name} : $Property->getDefaultValue();
		$value          = null;

		$Metadata = new TailorPropertyMetadata(
			$Property,
			$is_initialized,
		);

		// We don't recursively call attributes on attributes from this library to avoid an infinite loop.
		// There shouldn't be any attributes on attribute properties, but just in case.
		if (($Object instanceof DataProcessingAttributeInterface) === false)
		{
			$value = $this->processTailorAttributes($Property, $initial_value, $Metadata);
		}

		// This is here so we don't overwrite uninitialized properties if nothing changed.
		if ($initial_value === $value)
		{
			return;
		}

		$Object->{$property_name} = $value;
	}

	/**
	 * Process the Tailor attributes on a property.
	 *
	 * It processes attributes in order from top to bottom.
	 *
	 * @param ReflectionProperty     $Property The property which might have tailor attributes.
	 * @param mixed                  $value    The value that will be modified by the attributes.
	 * @param TailorPropertyMetadata $Metadata Any optional data that might be needed.
	 * @return mixed
	 */
	protected function processTailorAttributes(ReflectionProperty $Property, mixed $value, TailorPropertyMetadata $Metadata): mixed
	{
		$Attributes = $Property->getAttributes(AbstractTailorAttribute::class, ReflectionAttribute::IS_INSTANCEOF);

		foreach ($Attributes as $AttributeReflection) {
			$Attribute = $AttributeReflection->newInstance();

			$Attribute->Property       = $Metadata->Property;
			$Attribute->is_initialized = $Metadata->is_initialized;

			$value = $Attribute->process($value);
		}

		return $value;
	}
}