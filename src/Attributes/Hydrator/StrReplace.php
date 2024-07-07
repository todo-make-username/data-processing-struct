<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Abstracts\AbstractHydratorAttribute;

/**
 * Calls the str_replace() function on the value.
 *
 * The constructor args are the same ones used in str_replace. $subject and $count are omitted for obvious reasons.
 *
 * Can only be used on types that can be interpreted as a string. Others are ignored.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class StrReplace extends AbstractHydratorAttribute
{
	/**
	 * Uses the same args as the normal str_replace method.
	 *
	 * @param string|string[] $search  The value(s) to search for in the property's value.
	 * @param string|string[] $replace The replacement values(s) for ones that match $search.
	 */
	public function __construct(protected array|string $search, protected array|string $replace)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		if (is_string($value) === false && is_array($value) === false)
		{
			return $value;
		}

		$value = str_replace($this->search, $this->replace, $value);

		return $value;
	}
}