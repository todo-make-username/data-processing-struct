<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Validator\Settings;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\Abstracts\AbstractValidatorAttribute;
use TodoMakeUsername\DataProcessingStruct\Validator\Exceptions\ValidatorException;

/**
 * This contains the validation message when a property fails validation with the specific validation attribute.
 *
 * Without this, a validation attribute would provide a very generic message about what went wrong.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ValidatorFailureMessage
{
	/**
	 * The constructor
	 *
	 * @param string $attribute_class The class this message attribute is for.
	 * @param string $message         The message if the validation fails.
	 */
	public function __construct(
		public string $attribute_class,
		public string $message
	)
	{
		if (is_subclass_of($attribute_class, AbstractValidatorAttribute::class) === false)
		{
			throw new ValidatorException("'".$attribute_class."' must extend the AbstractValidatorAttribute class to be used with ValidatorFailureMessage");
		}
	}
}