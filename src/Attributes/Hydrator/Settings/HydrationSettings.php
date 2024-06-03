<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Settings;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Shared\DataProcessingAttributeInterface;

/**
 * This sets the various settings for hydration.
 *
 * This attribute can be applied to either the whole class or a specific property.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class HydrationSettings implements DataProcessingAttributeInterface
{
	/**
	 * Determine if this should be hydrated or not.
	 */
	public readonly bool $hydrate;

	/**
	 * Determine if values should be converted to the correct type or not.
	 */
	public readonly bool $convert;

	/**
	 * Set Any Hydration Settings
	 *
	 * @param boolean $hydrate Hydrate this or not.
	 * @param boolean $convert Convert this or not.
	 */
	public function __construct(bool $hydrate=true, bool $convert=true)
	{
		$this->hydrate = $hydrate;
		$this->convert = $convert;
	}
}