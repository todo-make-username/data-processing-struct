<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStructDemo\Objects;

use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\FileUpload;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Required;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Trim;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\NotEmpty;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\RegexMatch;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\Settings\ValidatorFailureMessage;
use TodoMakeUsername\DataProcessingStruct\Struct;

class BasicFormHandlingObj extends Struct implements ObjInterface
{
	#[Required]
	#[Trim]
	#[NotEmpty]
	#[ValidatorFailureMessage(NotEmpty::class, 'Name is required')]
	public string $name = '';

	#[Required]
	#[NotEmpty]
	#[RegexMatch('/^p[a|@|4][s|5]{2}w[o|0]rd$/i')]
	#[ValidatorFailureMessage(NotEmpty::class, 'Password is required')]
	#[ValidatorFailureMessage(RegexMatch::class, 'Password must be "password"')]
	public string $password = '';

	#[Required]
	#[NotEmpty]
	public int $age;

	public bool $enabled_daily_jokes = false;

	public array $pets = [];

	#[FileUpload]
	public array $profile_picture;

	#[Required]
	public bool $approved_tos;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'name'                => $this->name,
			'password'            => $this->password,
			'age'                 => $this->age,
			'enabled_daily_jokes' => $this->enabled_daily_jokes,
			'pets'                => $this->pets,
			'profile_picture'     => $this->profile_picture,
			'approved_tos'        => $this->approved_tos,
		];
	}
}