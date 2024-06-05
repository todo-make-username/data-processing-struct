<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Converter\ConversionException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorConversionToBoolTest extends TestCase
{
	public function testBoolConversion(): void
	{
		$hydrate_data = [
			'from_string' => 'on',
			'from_bool'   => true,
			'from_int'    => 0,
		];

		$expected = [
			'from_string' => true,
			'from_bool'   => true,
			'from_int'    => false,
		];

		$Obj = new class() extends Struct
		{
			public bool $from_string;
			public bool $from_bool;
			public bool $from_int;
		};

		$Obj->hydrate($hydrate_data);

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	public function testFancyBoolConversion(): void
	{
		$hydrate_data = [
			'from_on'    => 'on',
			'from_off'   => 'OfF',
			'from_yes'   => 'YES',
			'from_no'    => 'nO',
			'from_1'     => '1',
			'from_0'     => '0',
			'from_true'  => 'TrUe',
			'from_false' => 'fAlSe',
		];

		$expected = [
			'from_on'    => true,
			'from_off'   => false,
			'from_yes'   => true,
			'from_no'    => false,
			'from_1'     => true,
			'from_0'     => false,
			'from_true'  => true,
			'from_false' => false,
		];

		$Obj = new class() extends Struct
		{
			public bool $from_on;
			public bool $from_off;
			public bool $from_yes;
			public bool $from_no;
			public bool $from_1;
			public bool $from_0;
			public bool $from_true;
			public bool $from_false;
		};

		$Obj->hydrate($hydrate_data);

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	public function testBoolConversionFail(): void
	{
		$hydrate_data = [
			'from_float' => '123.123',
		];

		$Obj = new class() extends Struct
		{
			public bool $from_float;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert string to bool');

		$Obj->hydrate($hydrate_data);

		$this->fail('This should have thrown an exception');
	}

	public function testObjectToBoolConversion(): void
	{
		$hydrate_data = [
			'to_bool' => (new class() { public function __toString(){ return 'yes'; }}),
		];

		$expected = [
			'to_bool' => true,
		];

		$Obj = new class() extends Struct
		{
			public bool $to_bool;
		};

		$hydrated = $Obj->hydrate($hydrate_data);

		$this->assertTrue($hydrated);
		$this->assertSame($expected['to_bool'], $Obj->to_bool);
	}
}