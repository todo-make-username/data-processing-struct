<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Abstracts\AbstractHydratorAttribute;
use TodoMakeUsername\DataProcessingStruct\Converter\TypeConverter;
use TodoMakeUsername\DataProcessingStruct\Hydrator\Exceptions\HydrationException;

/**
 * This prematurely converts the value to the designated type.
 *
 * This is only needed if a premature type cast is needed, or if the property does not have a declared type and you want it to have one.
 * The type will always convert even without this attribute if the type is declared on the property.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ConvertTo extends AbstractHydratorAttribute
{
	/**
	 * The Typed Array Constructor
	 *
	 * @param string $type The scalar type to convert the value to [ int, bool, float, string ].
	 */
	public function __construct(protected string $type)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		$conversion_method = TypeConverter::getConversionMethodFromType($this->type);
		if ($conversion_method === null)
		{
			throw new HydrationException("The ConvertTo attribute does not support converting to (".$this->type."). Must be one of the following [ bool, int, float, string ]");
		}

		$new_value = $conversion_method($value);

		return $new_value;
	}
}