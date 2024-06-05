<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Converter\ConversionException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorConversionToIntTest extends TestCase
{
	public function testIntConversion(): void
	{
		$hydrate_data = [
			'from_string' => '123',
			'from_bool'   => true,
			'from_int'    => 321,
		];

		$expected = [
			'from_string' => 123,
			'from_bool'   => 1,
			'from_int'    => 321,
		];

		$Obj = new class() extends Struct
		{
			public int $from_string;
			public int $from_bool;
			public int $from_int;
		};

		$Obj->hydrate($hydrate_data);

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	public function testIntConversionFail(): void
	{
		$hydrate_data = [
			'from_float' => '123.123',
		];

		$Obj = new class() extends Struct
		{
			public int $from_float;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert string to int');

		$Obj->hydrate($hydrate_data);

		$this->fail('This should have thrown an exception');
	}

	public function testObjectToIntConversion(): void
	{
		$hydrate_data = [
			'to_int' => (new class() { public function __toString(){ return '321'; }}),
		];

		$expected = [
			'to_int' => 321,
		];

		$Obj = new class() extends Struct
		{
			public int $to_int;
		};

		$hydrated = $Obj->hydrate($hydrate_data);

		$this->assertTrue($hydrated);
		$this->assertSame($expected['to_int'], $Obj->to_int);
	}
}