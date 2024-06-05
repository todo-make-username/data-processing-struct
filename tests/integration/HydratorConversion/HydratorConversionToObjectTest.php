<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Converter\ConversionException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorConversionToObjectTest extends TestCase
{
	public function testObjectConversionFail(): void
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
}