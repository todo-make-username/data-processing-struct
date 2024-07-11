<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\ConvertTo;
use TodoMakeUsername\DataProcessingStruct\Hydrator\Exceptions\HydrationException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class ConvertToAttributeTest extends TestCase
{
	public function testArrayTypingSuccess(): void
	{
		$hydrate_data = [
			'int'    => '1',
			'bool'   => 'on',
			'float'  => '1.1',
			'string' => 1,
		];

		$expected = [
			'int'    => 1,
			'bool'   => true,
			'float'  => 1.1,
			'string' => '1',
		];

		$Obj = new class() extends Struct
		{
			#[ConvertTo('int')]
			public $int;

			#[ConvertTo('bool')]
			public $bool;
			
			#[ConvertTo('float')]
			public $float;
			
			#[ConvertTo('string')]
			public $string;
		};

		$Obj->hydrate($hydrate_data);

		foreach ($expected as $field => $value) {
			$this->assertSame($value, $Obj->$field);
		}
	}

	public function testIncompatibleType()
	{
		$Obj = new class() extends Struct
		{
			#[ConvertTo(DateTime::class)]
			public $test;
		};

		$this->expectException(HydrationException::class);
		$this->expectExceptionMessage('The ConvertTo attribute does not support converting to (DateTime). Must be one of the following [ bool, int, float, string ]');

		$Obj->hydrate([
			'test' => (new DateTime())
		]);

		$this->fail('This was supposed to throw an exception');
	}
}