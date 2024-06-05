<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\JsonDecode;
use TodoMakeUsername\DataProcessingStruct\Hydrator\Exceptions\HydrationException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class JsonDecodeAttributeTest extends TestCase
{
	/**
	 * @dataProvider arrayDataProvider
	 */
	public function testJsonConversionSuccess(mixed $test_data, mixed $expected): void
	{
		$hydrate_data = [
			'json' => $test_data,
		];

		$Obj = new class() extends Struct
		{
			#[JsonDecode(true)]
			public array $json;
		};

		$Obj->hydrate($hydrate_data);

		$this->assertSame($expected, $Obj->json);
	}

	public static function arrayDataProvider(): array
	{
		return [
			'json' => [
				'{"A":"1","B":2}',
				[ 'A' => '1', 'B' => 2 ],
			],
			'already an array' => [
				[],
				[],
			],
			'empty, but empty conversion takes over' => [
				'',
				[],
			],
		];
	}

	public function testIncompatibleTypeFail(): void
	{
		$hydrate_data = [
			'field1' => (new DateTime()),
		];

		$Obj = new class() extends Struct
		{
			#[JsonDecode(true)]
			public array $field1;
		};

		$this->expectException(HydrationException::class);
		$this->expectExceptionMessage("'field1' requires an array or a JSON compatible string.");

		$Obj->hydrate($hydrate_data);

		$this->fail('This was supposed to throw an exception');
	}

	public function testBadJson(): void
	{
		$hydrate_data = [
			'field1' => '{"A":"1","B":2}}}}}}}}}}}}}}}}}',
		];

		$Obj = new class() extends Struct
		{
			#[JsonDecode(true)]
			public array $field1;
		};

		$this->expectException(HydrationException::class);
		$this->expectExceptionMessage("Failed to hydrate 'field1', a valid JSON string is required.");

		$Obj->hydrate($hydrate_data);

		$this->fail('This was supposed to throw an exception');
	}
}