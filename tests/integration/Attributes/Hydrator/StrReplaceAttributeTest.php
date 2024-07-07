<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\StrReplace;
use TodoMakeUsername\DataProcessingStruct\Struct;

class StrReplaceAttributeTest extends TestCase
{
	public function testStrReplace(): void
	{
		$Obj = new class() extends Struct
		{
			#[StrReplace('R1', 'Hello')]
			public $field1;
		};

		$Obj->hydrate([
			'field1' => 'R1 World!',
		]);

		$this->assertSame('Hello World!', $Obj->field1);
	}

	public function testStrReplaceMultiple(): void
	{
		$Obj = new class() extends Struct
		{
			#[StrReplace([ 'R1', 'R2', '?' ], [ 'Hello', 'World', '!'])]
			public $field1;
		};

		$Obj->hydrate([
			'field1' => 'R1 R2?',
		]);

		$this->assertSame('Hello World!', $Obj->field1);
	}

	public function testStrReplaceNotStringIgnored(): void
	{
		$Obj = new class() extends Struct
		{
			#[StrReplace('R1', 'Hello')]
			public int $field1;
		};

		$Obj->hydrate([
			'field1' => 123,
		]);

		$this->assertSame(123, $Obj->field1);
	}

	public function testStrReplaceArrayMultiple(): void
	{
		$Obj = new class() extends Struct
		{
			#[StrReplace([ 'R1', 'R2', '?' ], [ 'Hello', 'World', '!'])]
			public $field1;
		};

		$expected = [
			'Hello World!',
			'Hello',
			'World',
			'!',
		];

		$Obj->hydrate([
			'field1' => ['R1 R2?', 'R1', 'R2', '?'],
		]);

		$this->assertSame($expected, $Obj->field1);
	}
}