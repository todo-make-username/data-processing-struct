<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Converter;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

class TypeConverter
{
	/**
	 * A map of methods to call to convert a value to the specified type.
	 *
	 * If not in this list, it is a type that doesn't need to be converted (mixed, no type). Or it is an unsupported conversion (objects, union typing) which should use a hydration attribute to set instead.
	 *
	 * @var array<string,string>
	 */
	protected static array $type_method_map = [
		'string'  => 'convertToString',
		'bool'    => 'convertToBool',
		'boolean' => 'convertToBool',
		'int'     => 'convertToInt',
		'integer' => 'convertToInt',
		'float'   => 'convertToFloat',
		'double'  => 'convertToFloat',
		'array'   => 'convertToEmptyArray', // Only empty value conversion. Everything else needs to use Hydration Attributes like the provided JsonDecode.
	];

	/**
	 * Convert a value to one that matches the Reflection Type(s).
	 *
	 * **UNEXPECTED PHP BEHAVIOR WARNING:** When using union types, the order returned by ReflectionUnionType::getTypes() is not the same order as declared in the code. PHP has a weighted order it returns each time. Check the PHP docs for more info.
	 *
	 * Union type conversions will try each type until one is successful. If they all fail, an error is thrown.
	 *
	 * Intersection Types and objects are not supported, the value is simply returned as is. Use a hydration attribute for those.
	 *
	 * @param mixed               $value          The value to convert.
	 * @param ReflectionType|null $ReflectionType The Reflection type.
	 * @return mixed
	 *
	 * @throws ConversionException If the value failed to convert properly to the specified type.
	 */
	public static function convertValueToPropertyType(mixed $value, ReflectionType|null $ReflectionType): mixed
	{
		if (
			$ReflectionType === null
			|| ($ReflectionType instanceof ReflectionIntersectionType)
			|| ($ReflectionType->allowsNull() && $value === null)
		)
		{
			return $value;
		}

		$possible_types = self::getPossibleTypes($ReflectionType);
		$type_methods   = self::getConversionMethodsToCall($possible_types);

		$converted = false;
		foreach($type_methods as $type_method)
		{
			try {
				$value = self::callConversionMethod($type_method, $value);
			} catch (ConversionException $th) {
				continue;
			}

			$converted = true;
			break;
		}

		if ($converted === false)
		{
			throw new ConversionException('Failed to convert '.gettype($value).' to '.join('|', $possible_types));
		}

		return $value;
	}

	/**
	 * Wrapper to call the conversion method.
	 *
	 * @param string $method The conversion method to call.
	 * @param mixed  $value  The value to convert.
	 * @return mixed
	 */
	protected static function callConversionMethod(string $method, mixed $value): mixed
	{
		return self::$method($value);
	}

	/**
	 * Get all possible types from reflection types.
	 *
	 * @param ReflectionType $ReflectionType The Reflection type object.
	 * @return array<string>
	 */
	protected static function getPossibleTypes(ReflectionType $ReflectionType): array
	{
		$possible_types   = [];
		$reflection_types = [];

		if ($ReflectionType instanceof ReflectionUnionType)
		{
			$reflection_types = $ReflectionType->getTypes();
		}
		elseif ($ReflectionType instanceof ReflectionNamedType) {
			$reflection_types[] = $ReflectionType;
		}

		foreach ($reflection_types as $named_type) {
			if ($named_type instanceof ReflectionNamedType)
			{
				$possible_types[] = $named_type->getName();
			}
		}

		return array_unique($possible_types);
	}

	/**
	 * Get all methods to call using an array of types.
	 *
	 * @param array<string> $types Data types to get the conversion methods for.
	 * @return array<string>
	 */
	protected static function getConversionMethodsToCall(array $types): array
	{
		$methods = [];
		foreach ($types as $type) {
			$type = strtolower($type);
			if (array_key_exists($type, self::$type_method_map) === true)
			{
				$methods[] = self::$type_method_map[$type];
			}
		}

		return array_unique($methods);
	}

	/**
	 * Convert value to a string
	 *
	 * @param mixed $value The value to convert.
	 * @return string
	 *
	 * @throws ConversionException When the conversion.
	 */
	public static function convertToString(mixed $value): string
	{
		$new_value = '';

		// If it is an object with a toString method, convert it via toString.
		if (gettype($value) === 'object' && method_exists($value, '__toString') === true)
		{
			$value = (string) $value;
			return $value;
		}

		// For everything else.
		if (is_scalar($value) === true || is_null($value) === true) {
			$new_value = (string) $value;
		} else {
			throw new ConversionException('Cannot convert '.gettype($value).' to string.');
		}

		$value = $new_value;
		return $value;
	}

	/**
	 * Convert value to an integer.
	 *
	 * Empty values are returned as 0
	 *
	 * @param mixed $value The value to convert.
	 * @return integer
	 *
	 * @throws ConversionException When the conversion.
	 */
	public static function convertToInt(mixed $value): int
	{
		if (empty($value) === true) {
			return 0;
		}

		// filter_var INT Note: Characters after numbers will work, but not the other way around:
		//     aa123 will fail, 123aa will convert to 123.
		$new_value = filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

		if (is_null($new_value))
		{
			throw new ConversionException('Failed to convert '.gettype($value).' to int');
		}

		$value = $new_value;
		return $value;
	}

	/**
	 * Convert value to a float
	 *
	 * Empty values are returned as 0.0
	 *
	 * @param mixed $value The value to convert.
	 * @return float
	 *
	 * @throws ConversionException When the conversion.
	 */
	public static function convertToFloat(mixed $value): float
	{
		if (empty($value) === true) {
			return 0.0;
		}

		$new_value = filter_var($value, FILTER_VALIDATE_FLOAT, (FILTER_FLAG_ALLOW_THOUSAND | FILTER_NULL_ON_FAILURE));

		if (is_null($new_value) === true)
		{
			throw new ConversionException('Failed to convert '.gettype($value).' to float');
		}

		$value = $new_value;
		return $value;
	}

	/**
	 * Convert value to a boolean
	 *
	 * @param mixed $value The value to convert.
	 * @return boolean
	 *
	 * @throws ConversionException When the conversion.
	 */
	public static function convertToBool(mixed $value): bool
	{
		// This also checks for on/off, yes/no, "true"/"false", 1/0
		$new_value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

		if (is_null($new_value) === true)
		{
			throw new ConversionException('Failed to convert '.gettype($value).' to bool');
		}

		$value = $new_value;
		return $value;
	}

	/**
	 * Convert an empty value to an empty array.
	 *
	 * Empty values only. Everything else will fail.
	 *
	 * @param mixed $value The value to convert.
	 * @return array<mixed>
	 *
	 * @throws ConversionException When the conversion.
	 */
	public static function convertToEmptyArray(mixed $value): array
	{
		$new_value = $value ?: [];

		if (is_array($new_value) === false)
		{
			throw new ConversionException('Failed to convert '.gettype($value).' to an empty array.');
		}

		$value = $new_value;
		return $value;
	}
}