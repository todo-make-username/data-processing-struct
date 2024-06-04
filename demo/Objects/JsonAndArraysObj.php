<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStructDemo\Objects;

use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\JsonDecode;
use TodoMakeUsername\DataProcessingStruct\Struct;

class JsonAndArraysObj extends Struct implements ObjInterface
{
	#[JsonDecode(true)]
	public array $val_array;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'array' => $this->val_array,
		];
	}
}