<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct;

use TodoMakeUsername\DataProcessingStruct\Hydrator\Traits\HydratorTrait;

abstract class Struct
{
	use HydratorTrait;

	/**
	 * Constructor for the struct, optionally can take data to load immediately.
	 *
	 * @param array<string,mixed> $load_data The data to load into this struct. array<property_name, value> [Optional].
	 */
	public function __construct(array $load_data=[])
	{
		if (count($load_data) > 0)
		{
			$this->hydrate($load_data);
		}
	}
}