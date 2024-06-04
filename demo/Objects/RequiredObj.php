<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStructDemo\Objects;

use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Required;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\NotEmpty;
use TodoMakeUsername\DataProcessingStruct\Struct;

class RequiredObj extends Struct implements ObjInterface
{
	#[Required]
	public string $required;

	#[Required]
	#[NotEmpty]
	public string $required_not_empty;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'required'           => $this->required,
			'required_not_empty' => $this->required_not_empty,
		];
	}
}