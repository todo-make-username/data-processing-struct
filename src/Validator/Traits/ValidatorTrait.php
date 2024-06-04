<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Validator\Traits;

use TodoMakeUsername\DataProcessingStruct\Validator\ValidationResponse;
use TodoMakeUsername\DataProcessingStruct\Validator\ValidatorHelper;

trait ValidatorTrait
{
	/**
	 * Run all validation attributes on this stuct's public properties.
	 *
	 * @return ValidationResponse
	 */
	public function validate(): ValidationResponse
	{
		$Helper = new ValidatorHelper();

		return $Helper->validateObject($this);
	}
}