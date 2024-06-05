<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorConversionToUnionTest extends TestCase
{
	public function testUnionTypes(): void
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
}