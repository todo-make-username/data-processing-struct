<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Tailor;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Tailor\Abstracts\AbstractTailorAttribute;

/**
 * Calls the trim() function on the value.
 *
 * Can only be used on types that can be interpreted as a string. Others are ignored.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Trim extends AbstractTailorAttribute
{
	/**
	 * Uses the same args as the normal trim method.
	 *
	 * @param string|null $characters Any Optional characters to trim.
	 */
	public function __construct(protected ?string $characters=null)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		if (is_string($value) === false)
		{
			return $value;
		}

		if (is_null($this->characters) === true)
		{
			$value = trim($value);
		}
		else
		{
			$value = trim($value, $this->characters);
		}

		return $value;
	}
}