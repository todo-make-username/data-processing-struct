<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStructDemo\Objects;

use TodoMakeUsername\DataProcessingStruct\Attributes\Tailor\Trim;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\NotEmpty;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\RegexMatch;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\ValidatorFailureMessage;
use TodoMakeUsername\DataProcessingStruct\Struct;

class ValidatorExampleObj extends Struct implements ObjInterface
{
	#[NotEmpty]
	public $not_empty;

	#[Trim]
	#[NotEmpty]
	public string $trimmed_not_empty = '';

	#[RegexMatch(pattern: '/^\d+[A-Za-z]+$/')]
	public string $pattern;

	#[NotEmpty]
	#[ValidatorFailureMessage(NotEmpty::class, 'This is my custom error message!')]
	public $custom_message;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'not_empty'         => $this->not_empty,
			'trimmed_not_empty' => $this->trimmed_not_empty,
			'pattern'           => $this->pattern,
			'custom_message'    => $this->custom_message,
		];
	}
}