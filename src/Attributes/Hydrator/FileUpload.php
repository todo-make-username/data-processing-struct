<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Abstracts\AbstractHydratorAttribute;
use TodoMakeUsername\DataProcessingStruct\Util\FilesHelper;

/**
 * This takes the file array from the $_FILES array that matches the property name.
 *
 * Can only be used on duck, mixed, or array types.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class FileUpload extends AbstractHydratorAttribute
{
	/**
	 * The File Upload Constructor
	 *
	 * @param boolean $transpose This will format the multi-upload array into a cleaner array format to work with. No effect on single files.
	 */
	public function __construct(protected bool $transpose=false)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		$property_name = $this->Property->name;

		$value = FilesHelper::getRawFileData($property_name);

		if ($this->transpose === true)
		{
			$value = FilesHelper::formatMultiFileData($value);
		}

		return $value;
	}
}