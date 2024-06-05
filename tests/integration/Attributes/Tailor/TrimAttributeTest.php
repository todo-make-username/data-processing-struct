<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Tailor\Trim;
use TodoMakeUsername\DataProcessingStruct\Struct;

class TrimAttributeTest extends TestCase
{
	public function testTrim(): void
	{
		$Obj = new class() extends Struct
		{
			#[Trim]
			public $field1 = '  abc ';
		};

		$Obj->tailor();

		$this->assertSame('abc', $Obj->field1);
	}

	public function testNotStringIgnored(): void
	{
		$Obj = new class() extends Struct
		{
			#[Trim]
			public int $field1 = 123;
		};

		$Obj->tailor();

		$this->assertSame(123, $Obj->field1);
	}

	public function testTrimOtherCharacters(): void
	{
		$Obj = new class() extends Struct
		{
			#[Trim('!')]
			public string $field1 = '!Hello World!!';
		};

		$Obj->tailor();

		$this->assertSame('Hello World', $Obj->field1);
	}
}