<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\HtmlSpecialChars;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HtmlSpecialCharsAttributeTest extends TestCase
{
	public function testHtmlSpecialChars(): void
	{
		$Obj = new class() extends Struct
		{
			#[HtmlSpecialChars()]
			public $field1;
		};

		$js_string   = '<script>alert("hax");</script>';

		$Obj->hydrate([
			'field1' => $js_string,
		]);

		$this->assertSame(htmlspecialchars($js_string), $Obj->field1);
	}

	public function testHtmlSpecialCharsNotString(): void
	{
		$Obj = new class() extends Struct
		{
			#[HtmlSpecialChars()]
			public $field1;
		};

		$int_val = 123;

		$Obj->hydrate([
			'field1' => $int_val,
		]);

		$this->assertSame($int_val, $Obj->field1);
	}
}