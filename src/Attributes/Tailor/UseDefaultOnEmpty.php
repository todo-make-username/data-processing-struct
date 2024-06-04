<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Tailor;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Tailor\Abstracts\AbstractTailorAttribute;
use TodoMakeUsername\DataProcessingStruct\Tailor\Exceptions\TailorException;

/**
 * Sets the value to the default value of the property if it passes an empty() check.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class UseDefaultOnEmpty extends AbstractTailorAttribute
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
			throw new TailorException('The property: "'.$property_name.'" must have a default value for the DefaultOnEmpty attribute.');
		}

		if (empty($value) === true)
		{
			$value = $this->Property->getDefaultValue();
		}

		return $value;
	}
}