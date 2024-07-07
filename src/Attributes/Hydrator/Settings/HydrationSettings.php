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
	 * Determine if values should be converted with the enhanced conversion or to use PHP's type coercion.
	 *
	 * This will handle various values that can be converted to bools, and for arrays it will convert empty values to an empty array.
	 */
	public readonly bool $enhanced_conversion;

	/**
	 * Set Any Hydration Settings
	 *
	 * @param boolean $hydrate             Hydrate this or not.
	 * @param boolean $enhanced_conversion Convert using an expanded set of type conversion rules, or disable to use PHP's type coercion instead.
	 */
	public function __construct(bool $hydrate=true, bool $enhanced_conversion=true)
	{
		$this->hydrate             = $hydrate;
		$this->enhanced_conversion = $enhanced_conversion;
	}
}