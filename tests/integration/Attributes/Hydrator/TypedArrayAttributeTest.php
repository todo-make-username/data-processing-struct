<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\TypedArray;
use TodoMakeUsername\DataProcessingStruct\Converter\ConversionException;
use TodoMakeUsername\DataProcessingStruct\Hydrator\Exceptions\HydrationException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class TypedArrayAttributeTest extends TestCase
{
	public function testArrayTypingSuccess(): void
	{
		$hydrate_data = [
			'int_array'    => [
				1, true, '0', 1.0, null
			],
			'bool_array'   => [
				1, true, '0', 1.0, null
			],
			'float_array'  => [
				1, true, '0', 1.0, null
			],
			'string_array' => [
				1, true, '0', 1.0, null
			],
			'duck_array'   => [
				1, true, '0', 1.0, null
			],
			'empty_array'  => [],
		];

		$expected = [
			'int_array'    => [
				1, 1, 0, 1, 0
			],
			'bool_array'   => [
				true, true, false, true, false
			],
			'float_array'  => [
				1.0, 1.0, 0.0, 1.0, 0.0
			],
			'string_array' => [
				'1', '1', '0', '1', '' // 1 not 1.0
			],
			'duck_array'   => [
				1, 1, 0, 1, 0
			],
			'empty_array'  => [],
		];

		$Obj = new class() extends Struct
		{
			#[TypedArray('int')]
			public array $int_array;

			#[TypedArray('bool')]
			public array $bool_array;
			
			#[TypedArray('float')]
			public array $float_array;
			
			#[TypedArray('string')]
			public array $string_array;

			#[TypedArray('int')]
			public $duck_array;

			#[TypedArray('string')]
			public array $empty_array;
		};

		$Obj->hydrate($hydrate_data);

		foreach ($expected as $field => $value) {
			$this->assertSame($value, $Obj->$field);
		}
	}

	public function testPropNotArrayType()
	{
		$Obj = new class() extends Struct
		{
			#[TypedArray('string')]
			public string $not_array;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert array to string');

		$Obj->hydrate([
			'not_array' => [ '1', '2' ]
		]);

		$this->fail('This was supposed to throw an exception');
	}

	public function testValueNotArrayType()
	{
		$Obj = new class() extends Struct
		{
			#[TypedArray('string')]
			public array $array;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert string to array');

		$Obj->hydrate([
			'array' => '123'
		]);

		$this->fail('This was supposed to throw an exception');
	}

	public function testIncompatibleType()
	{
		$Obj = new class() extends Struct
		{
			#[TypedArray(DateTime::class)]
			public array $array;
		};

		$this->expectException(HydrationException::class);
		$this->expectExceptionMessage('The TypedArray attribute does not support converting to (DateTime). Must be one of the following [ bool, int, float, string ]');

		$Obj->hydrate([
			'array' => [ new DateTime() ]
		]);

		$this->fail('This was supposed to throw an exception');
	}
}