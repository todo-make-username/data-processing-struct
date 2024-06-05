<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Converter\ConversionException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorConversionToMixedTest extends TestCase
{
	public function testMixedConversion(): void
	{
		$hydrate_data = [
			'from_string' => 'abc123',
			'from_bool'   => true,
			'from_int'    => 321,
			'from_float'  => 123.123,
			'from_array'  => [1,2,3],
		];

		$Obj = new class() extends Struct
		{
			public $from_string;
			public $from_bool;
			public $from_int;
			public $from_float;
			public $from_array;
		};

		$Obj->hydrate($hydrate_data);

		foreach (array_keys($hydrate_data) as $field)
		{
			$this->assertSame($hydrate_data[$field], $Obj->{$field}, $field);
		}
	}

	public function testObjectToVariousTypesConversion(): void
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