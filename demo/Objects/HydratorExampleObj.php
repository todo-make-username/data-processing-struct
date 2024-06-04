<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStructDemo\Objects;

use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\FileUpload;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\JsonDecode;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Required;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorExampleObj extends Struct implements ObjInterface
{
	#[Required]
	public $required;

	#[FileUpload]
	public array $file_single;

	#[JsonDecode(true)]
	public array $val_array;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'required'    => $this->required,
			'file_single' => $this->file_single,
			'val_array'   => $this->val_array,
		];
	}
}