<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Trim;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\UseValueOnEmpty;
use TodoMakeUsername\DataProcessingStruct\Converter\ConversionException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class UseValueOnEmptyAttributeTest extends TestCase
{
	public function testUseValueOnEmpty(): void
	{
		$Obj = new class() extends Struct
		{
			#[UseValueOnEmpty('default')]
			public $field1;
		};

		$Obj->hydrate([ 'field1' => '' ]);

		$this->assertSame('default', $Obj->field1);
	}

	public function testTrimWithUseValueOnEmpty(): void
	{
		$Obj = new class() extends Struct
		{
			#[Trim]
			#[UseValueOnEmpty('default')]
			public $field1;
		};

		$Obj->hydrate([ 'field1' => '            ' ]);

		$this->assertSame('default', $Obj->field1);
	}

	public function testUseValueOnEmptyInvalidType(): void
	{
		$Obj = new class() extends Struct
		{
			#[UseValueOnEmpty(123)]
			public array $field1;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert integer to array');

		$Obj->hydrate([ 'field1' => '' ]);

		$this->fail('This was supposed to throw an exception');
	}
}