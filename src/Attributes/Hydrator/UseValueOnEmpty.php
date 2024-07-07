<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Abstracts\AbstractHydratorAttribute;
use TodoMakeUsername\DataProcessingStruct\Hydrator\Exceptions\HydrationException;

/**
 * Sets the provided value if the current value passes an empty() check.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class UseValueOnEmpty extends AbstractHydratorAttribute
{
	/**
	 * Updates the current value with the provided value if the current value passes an empty() check.
	 *
	 * @param mixed $value The value to replace if the current one is empty.
	 */
	public function __construct(protected readonly mixed $value)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		if (empty($value) === true)
		{
			$value = $this->value;
		}

		return $value;
	}
}