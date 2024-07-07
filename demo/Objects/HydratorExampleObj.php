<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStructDemo\Objects;

use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\FileUpload;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\JsonDecode;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Required;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\StrReplace;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Trim;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\TypedArray;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\UseDefaultOnEmpty;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorExampleObj extends Struct implements ObjInterface
{
	#[Required]
	public $required;

	#[FileUpload]
	public array $file_single;

	#[JsonDecode(true)]
	public array $val_array;

	#[TypedArray('int')]
	public array $typed_array = [];

	#[Trim]
	public string $trim;

	#[UseDefaultOnEmpty]
	public string $default_on_empty = 'test';

	#[StrReplace('World', 'You')]
	public string $str_replace;

	#[Trim]
	#[UseDefaultOnEmpty]
	public string $useful_mix = 'I was empty';

	#[StrReplace([ 'H', 'W' ], [ 'Hello', 'World' ])]
	#[Trim()]
	public string $custom_mix;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'required'         => $this->required,
			'file_single'      => $this->file_single,
			'val_array'        => $this->val_array,
			'typed_array'      => $this->typed_array,
			'trim'             => $this->trim,
			'default_on_empty' => $this->default_on_empty,
			'str_replace'      => $this->str_replace,
			'useful_mix'       => $this->useful_mix,
			'custom_mix'       => $this->custom_mix,
		];
	}
}