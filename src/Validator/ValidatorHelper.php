<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Validator;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use TodoMakeUsername\DataProcessingStruct\Attributes\Shared\DataProcessingAttributeInterface;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\Abstracts\AbstractValidatorAttribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\Settings\ValidatorFailureMessage;
use TodoMakeUsername\DataProcessingStruct\Reflector\ObjectReflector;

class ValidatorHelper
{
	/**
	 * Process any validation attributes on an designated object.
	 *
	 * @param object $Object The object to process validation attributes on.
	 * @return ValidationResponse
	 */
	public function validateObject(object $Object): ValidationResponse
	{
		$ObjectReflector = new ObjectReflector(new ReflectionClass($Object));

		$success  = true;
		$messages = [];

		$Properties = $ObjectReflector->getObjectProperties(ReflectionProperty::IS_PUBLIC);
		foreach ($Properties as $Property)
		{
			$Response = $this->validateObjectProperty($Object, $Property);

			if (is_null($Response) === false)
			{
				$success                   = false;
				$messages[$Property->name] = $Response;
			}
		}

		return new ValidationResponse($success, $messages);
	}

	/**
	 * Validate an object's property.
	 *
	 * @param object             $Object   The object with the property to validate.
	 * @param ReflectionProperty $Property The property Reflection object.
	 * @return null|PropertyValidationResponse Returns null if validation passes. AKA no errors.
	 */
	protected function validateObjectProperty(object $Object, ReflectionProperty $Property): ?PropertyValidationResponse
	{
		$property_name  = $Property->name;
		$is_initialized = $Property->isInitialized($Object);
		$value          = ($is_initialized) ? $Object->{$property_name} : $Property->getDefaultValue();

		$Metadata = new ValidatorPropertyMetadata(
			$Property,
			$is_initialized,
		);

		// We don't recursively call attributes on attributes from this project to avoid an infinite loop.
		// There shouldn't be any attributes on attribute properties, but just in case.
		if (($Object instanceof DataProcessingAttributeInterface) === true)
		{
			return null;
		}

		return $this->processValidatorAttributes($Property, $value, $Metadata);
	}

	/**
	 * Process the Validator attributes on a property.
	 *
	 * It processes attributes in order from top to bottom.
	 *
	 * @param ReflectionProperty        $Property The property which might have validator attributes.
	 * @param mixed                     $value    The value that will be validated by the attributes.
	 * @param ValidatorPropertyMetadata $Metadata Any optional data that might be needed.
	 * @return null|PropertyValidationResponse
	 */
	protected function processValidatorAttributes(ReflectionProperty $Property, mixed $value, ValidatorPropertyMetadata $Metadata): ?PropertyValidationResponse
	{
		$Attributes = $Property->getAttributes(AbstractValidatorAttribute::class, ReflectionAttribute::IS_INSTANCEOF);

		$success  = true;
		$messages = [];

		$custom_fail_messages = (count($Attributes) > 0) ? $this->getCustomValidatorFailureMessages($Property) : [];

		foreach ($Attributes as $AttributeReflection) {
			$Attribute = $AttributeReflection->newInstance();

			$Attribute->Property       = $Metadata->Property;
			$Attribute->is_initialized = $Metadata->is_initialized;

			$attribute_success = $Attribute->validate($value);

			if ($attribute_success === false)
			{
				$success    = false;
				$messages[] = $custom_fail_messages[$Attribute::class] ?? $Attribute->getFailMessage();
			}
		}

		if ($success === true)
		{
			return null;
		}

		return new PropertyValidationResponse($value, $messages);
	}

	/**
	 * Get a map of all the validation classes to their custom validation message on a property.
	 *
	 * @param ReflectionProperty $Property The property which might have custom validation message attributes.
	 * @return array<string,string>
	 */
	protected function getCustomValidatorFailureMessages(ReflectionProperty $Property): array
	{
		$map = [];

		$ReflectionAttributes = $Property->getAttributes(ValidatorFailureMessage::class, ReflectionAttribute::IS_INSTANCEOF);
		foreach ($ReflectionAttributes as $ReflectionAttribute)
		{
			$Attribute = $ReflectionAttribute->newInstance();

			$map[$Attribute->attribute_class] = $Attribute->message;
		}

		return $map;
	}
}