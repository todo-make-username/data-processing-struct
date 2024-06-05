<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Converter\ConversionException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorConversionToFloatTest extends TestCase
{
	public function testFloatConversion(): void
	{
		$hydrate_data = [
			'from_string' => '123.123',
			'from_bool'   => true,
			'from_int'    => 321,
		];

		$expected = [
			'from_string' => 123.123,
			'from_bool'   => 1.0,
			'from_int'    => 321.0,
		];

		$Obj = new class() extends Struct
		{
			public float $from_string;
			public float $from_bool;
			public float $from_int;
		};

		$Obj->hydrate($hydrate_data);

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	public function testFloatConversionFail(): void
	{
		$hydrate_data = [
			'from_array' => [],
		];

		$Obj = new class() extends Struct
		{
			public float $from_array;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert array to float');

		$Obj->hydrate($hydrate_data);

		$this->fail('This should have thrown an exception');
	}

	public function testObjectToFloatConversion(): void
	{
		$hydrate_data = [
			'to_float' => (new class() { public function __toString(){ return '321.123'; }}),
		];

		$expected = [
			'to_float' => 321.123,
		];

		$Obj = new class() extends Struct
		{
			public float $to_float;
		};

		$hydrated = $Obj->hydrate($hydrate_data);

		$this->assertTrue($hydrated);
		$this->assertSame($expected['to_float'], $Obj->to_float);
	}
}