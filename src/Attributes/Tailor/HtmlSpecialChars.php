<?php declare(strict_types=1);

namespace TodoMakeUsername\DataProcessingStruct\Attributes\Tailor;

use Attribute;
use TodoMakeUsername\DataProcessingStruct\Attributes\Tailor\Abstracts\AbstractTailorAttribute;

/**
 * Calls the htmlspecialchars() function on the value.
 *
 * The constructor args are the same ones used in htmlspecialchars.
 *
 * Can only be used on strings. Others are ignored.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class HtmlSpecialChars extends AbstractTailorAttribute
{
	/**
	 * Convert special characters to HTML entities
	 *
	 * @param integer     $flags         Optional - A bitmask of one or more flags, which specify how to handle quotes, invalid code unit sequences and the used document type.
	 * @param string|null $encoding      Optional - An optional argument defining the encoding used when converting characters. If omitted, encoding defaults to the value of the default_charset configuration option.
	 * @param boolean     $double_encode Optional - When double_encode is turned off PHP will not encode existing html entities, the default is to convert everything.
	 */
	public function __construct(
		protected int $flags=ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
		protected ?string $encoding=null,
		protected bool $double_encode=true
	)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		if (is_string($value) === false)
		{
			return $value;
		}

		$value = htmlspecialchars($value, $this->flags, $this->encoding, $this->double_encode);

		return $value;
	}
}