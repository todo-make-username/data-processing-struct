<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Trim;
use TodoMakeUsername\DataProcessingStruct\Struct;

class TrimAttributeTest extends TestCase
{
	public function testTrim(): void
	{
		$Obj = new class() extends Struct
		{
			#[Trim]
			public $field1;
		};

		$Obj->hydrate([
			'field1' => '  abc ',
		]);

		$this->assertSame('abc', $Obj->field1);
	}

	public function testNotStringIgnored(): void
	{
		$Obj = new class() extends Struct
		{
			#[Trim]
			public int $field1;
		};

		$Obj->hydrate([
			'field1' => 123,
		]);

		$this->assertSame(123, $Obj->field1);
	}

	public function testTrimOtherCharacters(): void
	{
		$Obj = new class() extends Struct
		{
			#[Trim('!')]
			public string $field1;
		};

		$Obj->hydrate([
			'field1' => '!Hello World!!',
		]);

		$this->assertSame('Hello World', $Obj->field1);
	}
}