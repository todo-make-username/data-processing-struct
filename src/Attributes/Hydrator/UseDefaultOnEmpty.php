<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Abstracts\AbstractHydratorAttribute;
use TodoMakeUsername\DataProcessingStruct\Hydrator\Exceptions\HydrationException;

/**
 * Sets the value to the default value of the property if it passes an empty() check.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class UseDefaultOnEmpty extends AbstractHydratorAttribute
{
	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		$property_name = $this->Property->name;
		$has_default   = $this->Property->hasDefaultValue();

		if ($has_default === false)
		{
			throw new HydrationException('The property: "'.$property_name.'" must have a default value for the DefaultOnEmpty attribute.');
		}

		if (empty($value) === true)
		{
			$value = $this->Property->getDefaultValue();
		}

		return $value;
	}
}