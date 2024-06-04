<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Abstracts\AbstractHydratorAttribute;
use TodoMakeUsername\DataProcessingStruct\Hydrator\Exceptions\HydrationException;

/**
 * When a valid json string is passed in, turn it into an array.
 *
 * Can only be used on properties that can take an array.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class JsonDecode extends AbstractHydratorAttribute
{
	/**
	 * Decode a json string.
	 *
	 * All arguments match what PHP's json_decode takes.
	 *
	 * @phan-param int<1, max> $depth
	 *
	 * @param boolean|null $associative If this value should be parsed as an associative array.
	 * @param integer      $depth       Specified recursion depth.
	 * @param integer      $flags       Bit mask of JSON decode options.
	 */
	public function __construct(protected readonly ?bool $associative=null, protected readonly int $depth=512, protected readonly int $flags=0)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		// Don't do anything if already an array or the value is empty.
		if (is_array($value) === true || empty($value) === true)
		{
			return $value;
		}

		$property_name = $this->Property->name;

		if (is_string($value) === false)
		{
			throw new HydrationException("'{$property_name}' requires an array or a JSON compatible string.");
		}

		$json_value = json_decode(strval($value), $this->associative, $this->depth, $this->flags);
		if (json_last_error() === JSON_ERROR_NONE && empty($json_value) === false)
		{
			$value = $json_value;
		}

		if (is_array($value) === false)
		{
			throw new HydrationException("Failed to hydrate '{$property_name}', a valid JSON string is required.");
		}

		return $value;
	}
}