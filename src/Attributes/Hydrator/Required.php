<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Abstracts\AbstractHydratorAttribute;
use TodoMakeUsername\DataProcessingStruct\Hydrator\Exceptions\HydrationException;

/**
 * The hydration data array must have this property name set as a key.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Required extends AbstractHydratorAttribute
{
	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		$property_name = $this->Property->name;

		if ($this->value_exists === false)
		{
			throw new HydrationException("A value is required for '{$property_name}'.");
		}

		return $value;
	}
}