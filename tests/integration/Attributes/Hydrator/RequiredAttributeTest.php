<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Required;
use TodoMakeUsername\DataProcessingStruct\Hydrator\Exceptions\HydrationException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class RequiredAttributeTest extends TestCase
{
	public function testBasicRequired(): void
	{
		$hydrate_data = [
			'field1' => 'test1',
			'field2' => 2,
		];

		$Obj = new class() extends Struct
		{
			public $field1;

			#[Required]
			public $field2;
		};
		$Obj->hydrate($hydrate_data);

		$this->assertSame($hydrate_data['field1'], $Obj->field1);
		$this->assertSame($hydrate_data['field2'], $Obj->field2);
	}

	public function testBasicRequiredMissing(): void
	{
		$hydrate_data = [
			'field1' => 'test1',
		];

		$Obj = new class() extends Struct
		{
			public $field1;

			#[Required]
			public $field2;
		};

		$this->expectException(HydrationException::class);
		$this->expectExceptionMessage("A value is required for 'field2'.");

		$Obj->hydrate($hydrate_data);

		$this->fail('This was supposed to throw an exception');
	}
}