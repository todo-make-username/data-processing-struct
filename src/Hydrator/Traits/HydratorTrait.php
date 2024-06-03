<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Hydrator\Traits;

use TodoMakeUsername\DataProcessingStruct\Hydrator\HydrationHelper;

trait HydratorTrait
{
	/**
	 * Load data into this struct.
	 *
	 * array<property,value>
	 *
	 * @param array<string,mixed> $load_data The data to load into this struct.
	 * @return boolean If the object is hydrated.
	 */
	public function hydrate(array $load_data): bool
	{
		$Helper = new HydrationHelper();

		return $Helper->hydrateObject($this, $load_data);
	}
}