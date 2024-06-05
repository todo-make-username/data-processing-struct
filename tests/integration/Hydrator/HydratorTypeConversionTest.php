<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Settings\HydrationSettings;
use TodoMakeUsername\DataProcessingStruct\Converter\ConversionException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorTypeConversionTest extends TestCase
{
	public function testNoConversionSetting()
	{
		$hydrate_data = [
			'from_int' => 321,
		];

		$Obj = new class() extends Struct
		{
			#[HydrationSettings(convert: false)]
			public string $from_int;
		};

		$this->expectException(TypeError::class);
		$this->expectExceptionMessageMatches('/^Cannot assign int to property .*?::\$from_int of type string$/');

		$Obj->hydrate($hydrate_data);

		$this->fail('This should have thrown an exception');
	}

	public function testIntConversion()
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

	public function testIntConversionFail()
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

	/**
	 * Bool
	 */
	public function testBoolConversion()
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

	public function testFancyBoolConversion()
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

	public function testBoolConversionFail()
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

	/**
	 * Float
	 */
	public function testFloatConversion()
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

	public function testFloatConversionFail()
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

	/**
	 * String
	 */
	public function testStringConversion()
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

	public function testStringConversionFail()
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

	/**
	 * Mixed
	 */
	public function testMixedConversion()
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

	/**
	 * Arrays
	 */
	public function testEmptyArrayConversionSuccess()
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

	public function testArrayConversionFail()
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

	public function testObjectConversion()
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

	public function testObjectConversionFail()
	{
		$hydrate_data = [
			'to_string' => (new class() { }),
		];

		$Obj = new class() extends Struct
		{
			public string $to_string;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage("Failed to convert object to string");

		$Obj->hydrate($hydrate_data);

		$this->fail('This should have thrown an exception');
	}

	public function testUnionTypes()
	{
		$hydrate_test_data = [
			[ 'to_string_int' => 123, 'to_int_string' => '123' ],
			[ 'to_string_int' => '123', 'to_int_string' => 123 ],
		];

		// PHP has a preset order, in this case no matter the specified union order, string will always be evaluated first.
		// We want this to fail if that ever changes so we can update this library.
		$expected_data = [
			[ 'to_string_int' => '123', 'to_int_string' => '123' ],
			[ 'to_string_int' => '123', 'to_int_string' => '123' ],
		];

		$Obj = new class() extends Struct
		{
			public int|string $to_int_string;
			public string|int $to_string_int;
		};

		foreach ($hydrate_test_data as $ndx => $hydrate_data) {
			$Obj->hydrate($hydrate_data);

			foreach ($expected_data[$ndx] as $field => $expected) {
				$this->assertSame($expected, $Obj->{$field});
			}
		}
	}

	// public function testIntersectionTypes()
	// {

	// }
}