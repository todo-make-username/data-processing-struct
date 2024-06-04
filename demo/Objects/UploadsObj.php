<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStructDemo\Objects;

use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\FileUpload;
use TodoMakeUsername\DataProcessingStruct\Struct;

class UploadsObj extends Struct implements ObjInterface
{
	#[FileUpload]
	public array $upload_single;

	#[FileUpload]
	public array $upload_multiple;

	#[FileUpload(formatted_uploads: true)]
	public array $upload_multiple_formatted;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'single'          => $this->upload_single,
			'multi'           => $this->upload_multiple,
			'multi formatted' => $this->upload_multiple_formatted,
		];
	}
}