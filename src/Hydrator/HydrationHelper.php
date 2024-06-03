<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Hydrator;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Abstracts\AbstractHydratorAttribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Settings\HydrationSettings;
use TodoMakeUsername\DataProcessingStruct\Attributes\Shared\DataProcessingAttributeInterface;
use TodoMakeUsername\DataProcessingStruct\Converter\TypeConverter;
use TodoMakeUsername\DataProcessingStruct\Reflector\ObjectReflector;
use TodoMakeUsername\DataProcessingStruct\Reflector\PropertyReflector;

class HydrationHelper
{
	/**
	 * Hydrate an object with data.
	 *
	 * @param object              $Object    The object to hydrate.
	 * @param array<string,mixed> $load_data The data to hydrate the object with.
	 * @return boolean
	 */
	public function hydrateObject(object $Object, array $load_data): bool
	{
		$ObjectReflector = new ObjectReflector(new ReflectionClass($Object));

		/** @var HydrationSettings */
		$ClassSettings = $ObjectReflector->getObjectAttribute(HydrationSettings::class)?->newInstance() ?? new HydrationSettings();

		if ($ClassSettings->hydrate === false)
		{
			return false;
		}

		$Properties = $ObjectReflector->getObjectProperties(ReflectionProperty::IS_PUBLIC);

		foreach ($Properties as $Property)
		{
			// Get value metadata
			$value_exists = array_key_exists($Property->name, $load_data);
			$value        = ($value_exists === true) ? $load_data[$Property->name] : null;

			// Get the hydration settings on the property, or use the class settings as the default if not set.
			/** @var HydrationSettings */
			$PropertySettings = (new PropertyReflector($Property))->getPropertyAttribute(HydrationSettings::class)?->newInstance() ?? $ClassSettings;

			$Metadata = $this->getPropertyMetadata($Property, $value_exists, $value, $PropertySettings);

			if ($this->hydrateObjectProperty($Object, $Property, $value, $Metadata) === false)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Hydrate an object's property.
	 *
	 * @param object                   $Object           The object with the property to hydrate.
	 * @param ReflectionProperty       $Property         The property Reflection object.
	 * @param mixed                    $value            The value to hydrate the property with.
	 * @param HydratorPropertyMetadata $PropertyMetadata Various pieces of metadata needed for the property to be hydrated.
	 * @return boolean Return false on error.
	 */
	protected function hydrateObjectProperty(object $Object, ReflectionProperty $Property, mixed $value, HydratorPropertyMetadata $PropertyMetadata): bool
	{
		// Skip Hydrating if hydrate is set to false.
		if ($PropertyMetadata->hydrate === false)
		{
			return true;
		}

		// We don't recursively hydrate attributes that implement DataProcessingAttributeInterface to avoid an infinite loop.
		if (($Object instanceof DataProcessingAttributeInterface) === false)
		{
			$value = $this->processHydrationAttributes($Property, $value, $PropertyMetadata);
		}

		// If the value wasn't passed in, and the hydration attributes didn't change the value, don't set anything.
		if ($PropertyMetadata->value_exists === false && $PropertyMetadata->preprocessed_value === $value)
		{
			return true;
		}

		if ($PropertyMetadata->convert === true)
		{
			$value = TypeConverter::convertValueToPropertyType($value, $Property->getType());
		}

		$Object->{$Property->name} = $value;

		return true;
	}

	/**
	 * Get Metadata object for the property that contains various settings and info about the property and the hydration value.
	 *
	 * @param ReflectionProperty $Property           The reflection of the property.
	 * @param boolean            $exists             If the value was set in the hydration array.
	 * @param mixed              $preprocessed_value The value that was passed in with the hydration array, or null if not set.
	 * @param HydrationSettings  $Settings           The Hydration Settings for the property.
	 * @return HydratorPropertyMetadata
	 */
	protected function getPropertyMetadata(ReflectionProperty $Property, bool $exists, mixed $preprocessed_value, HydrationSettings $Settings): HydratorPropertyMetadata
	{
		$Metadata = new HydratorPropertyMetadata(
			$Property,
			$exists,
			$preprocessed_value,
			$Settings->hydrate,
			$Settings->convert,
		);

		return $Metadata;
	}

	/**
	 * Process the value using the hydration attributes on the property.
	 *
	 * @param ReflectionProperty       $Property The reflection of the property that is being set.
	 * @param mixed                    $value    The value to transform via hydration attributes.
	 * @param HydratorPropertyMetadata $Metadata Hydration metadata.
	 * @return mixed
	 */
	protected function processHydrationAttributes(ReflectionProperty $Property, mixed $value, HydratorPropertyMetadata $Metadata): mixed
	{
		$Attributes = $Property->getAttributes(AbstractHydratorAttribute::class, ReflectionAttribute::IS_INSTANCEOF);

		foreach ($Attributes as $AttributeReflection) {
			$Attribute = $AttributeReflection->newInstance();

			$Attribute->Property     = $Metadata->Property;
			$Attribute->value_exists = $Metadata->value_exists;

			$value = $Attribute->process($value);
		}

		return $value;
	}
}