<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Validator;

use JsonSerializable;

class ValidationResponse implements JsonSerializable
{
	/**
	 * If the validation was successful.
	 */
	public readonly bool $success;

	/**
	 * All failure messages on each property that occurred during validation.
	 *
	 * @var array<string,PropertyValidationResponse>
	 */
	public readonly array $messages;

	/**
	 * The Validation Response Class Constructor
	 *
	 * @param boolean                                  $success  If the validation was successful.
	 * @param array<string,PropertyValidationResponse> $messages Any validation messages for each property.
	 */
	public function __construct(bool $success, array $messages)
	{
		$this->success  = $success;
		$this->messages = $messages;
	}

	/**
	 * Return all validation failure messages.
	 *
	 * @return array<string>
	 */
	public function getAllMessages(): array
	{
		$messages = [];

		foreach ($this->messages as $PropertyResponse) {
			array_push($messages, ...$PropertyResponse->messages);
		}

		return $messages;
	}

	/**
	 * Specify data which should be serialized to JSON.
	 *
	 * @return array<string,bool|array<string,mixed|array<string>>>
	 */
	public function jsonSerialize(): array
	{
		$messages = [];

		foreach ($this->messages as $field => $Response) {
			$messages[$field] = $Response->jsonSerialize();
		}

		return [
			'success'  => $this->success,
			'messages' => $messages,
		];
	}
}