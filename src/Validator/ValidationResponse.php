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
	 * The responses for each property that failed validation.
	 *
	 * @var array<string,PropertyValidationResponse>
	 */
	public readonly array $property_responses;

	/**
	 * The Validation Response Class Constructor
	 *
	 * @param boolean                                  $success            If the validation was successful.
	 * @param array<string,PropertyValidationResponse> $property_responses Any validation responses for each property.
	 */
	public function __construct(bool $success, array $property_responses)
	{
		$this->success            = $success;
		$this->property_responses = $property_responses;
	}

	/**
	 * Return all validation failure messages.
	 *
	 * @return array<string>
	 */
	public function getAllMessages(): array
	{
		$messages = [];

		foreach ($this->property_responses as $PropertyResponse) {
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
		$property_responses = [];

		foreach ($this->property_responses as $field => $Response) {
			$property_responses[$field] = $Response->jsonSerialize();
		}

		return [
			'success'            => $this->success,
			'property_responses' => $property_responses,
		];
	}
}