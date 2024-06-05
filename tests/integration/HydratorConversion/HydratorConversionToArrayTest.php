<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Converter\ConversionException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorConversionToArrayTest extends TestCase
{
	public function testEmptyArrayConversionSuccess(): void
	{
		$hydrate_data = [
			'field1' => '',
			'field2' => null,
		];

		$Obj = new class() extends Struct
		{
			public array $field1;
			public array $field2;
		};

		$Obj->hydrate($hydrate_data);

		$this->assertSame([], $Obj->field1);
		$this->assertSame([], $Obj->field2);
	}

	public function testArrayConversionFail(): void
	{
		$hydrate_data = [
			'field1' => 'not empty',
		];

		$Obj = new class() extends Struct
		{
			public array $field1;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert string to array');

		$Obj->hydrate($hydrate_data);

		$this->fail();
	}
}