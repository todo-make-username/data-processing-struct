<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Converter\ConversionException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorConversionToStringTest extends TestCase
{
	public function testStringConversion(): void
	{
		$hydrate_data = [
			'from_string' => '123.123',
			'from_bool'   => true,
			'from_int'    => 321,
		];

		$expected = [
			'from_string' => '123.123',
			'from_bool'   => '1',
			'from_int'    => '321',
		];

		$Obj = new class() extends Struct
		{
			public string $from_string;
			public string $from_bool;
			public string $from_int;
		};

		$Obj->hydrate($hydrate_data);

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	public function testStringConversionFail(): void
	{
		$hydrate_data = [
			'from_obj' => (new DateTime()),
		];

		$Obj = new class() extends Struct
		{
			public string $from_obj;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert object to string');

		$Obj->hydrate($hydrate_data);

		$this->fail('This should have thrown an exception');
	}

	public function testObjectConversion(): void
	{
		$hydrate_data = [
			'to_string' => (new class() { public function __toString(){ return 'test'; }}),
			'to_bool'   => (new class() { public function __toString(){ return 'yes'; }}),
			'to_int'    => (new class() { public function __toString(){ return '321'; }}),
			'to_float'  => (new class() { public function __toString(){ return '321.123'; }}),
		];

		$expected = [
			'to_string' => 'test',
			'to_bool'   => true,
			'to_int'    => 321,
			'to_float'  => 321.123,
		];

		$Obj = new class() extends Struct
		{
			public string $to_string;
			public bool   $to_bool;
			public int    $to_int;
			public float  $to_float;
		};

		$hydrated = $Obj->hydrate($hydrate_data);

		$this->assertTrue($hydrated);
		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}
}