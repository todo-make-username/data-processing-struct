<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Tailor\Traits;

use TodoMakeUsername\DataProcessingStruct\Tailor\TailorHelper;

trait TailorTrait
{
	/**
	 * Run any altering attributes on this stuct's public properties.
	 *
	 * @return void
	 */
	public function tailor(): void
	{
		$Helper = new TailorHelper();

		$Helper->tailorObject($this);
	}
}