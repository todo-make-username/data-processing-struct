<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Abstracts\AbstractHydratorAttribute;
use TodoMakeUsername\DataProcessingStruct\Converter\TypeConverter;
use TodoMakeUsername\DataProcessingStruct\Hydrator\Exceptions\HydrationException;

/**
 * This converts all the array elements to the designated scalar type.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class TypedArray extends AbstractHydratorAttribute
{
	/**
	 * The Typed Array Constructor
	 *
	 * @param string $type The scalar type to convert all the array elements to [ int, bool, float, string ].
	 */
	public function __construct(protected string $type)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		if (is_array($value) === false)
		{
			return $value;
		}

		$conversion_method = TypeConverter::getConversionMethodFromType($this->type);
		if ($conversion_method === null)
		{
			throw new HydrationException("The TypedArray attribute does not support converting to (".$this->type."). Must be one of the following [ bool, int, float, string ]");
		}

		$new_value = array_map($conversion_method, $value);

		return $new_value;
	}
}