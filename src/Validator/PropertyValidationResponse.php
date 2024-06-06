<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Validator;

use JsonSerializable;

class PropertyValidationResponse implements JsonSerializable
{
	/**
	 * The value that failed the validation.
	 */
	public readonly mixed $value;

	/**
	 * All validation messages for a property.
	 *
	 * @var array<string>
	 */
	public readonly array $messages;

	/**
	 * The Property Validation Response Class Constructor
	 *
	 * @param mixed         $value    The value that triggered the failure.
	 * @param array<string> $messages Any validation messages for a property.
	 */
	public function __construct(mixed $value, array $messages)
	{
		$this->value    = $value;
		$this->messages = $messages;
	}

	/**
	 * Specify data which should be serialized to JSON.
	 *
	 * @return array<string,mixed|array<string>>
	 */
	public function jsonSerialize(): array
	{
		return [
			'value'    => $this->value,
			'messages' => $this->messages,
		];
	}
}