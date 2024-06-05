<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Settings\HydrationSettings;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorTest extends TestCase
{
	public function testBasicHydration(): void
	{
		$hydrate_data = [
			'field1' => 'test1',
			'field2' => 2,
		];

		$Obj = new class() extends Struct
		{
			public $field1;
			public $field2;
		};

		$hydrated = $Obj->hydrate($hydrate_data);

		$this->assertTrue($hydrated);
		$this->assertSame($hydrate_data['field1'], $Obj->field1);
		$this->assertSame($hydrate_data['field2'], $Obj->field2);
	}

	public function testBasicHydrationNoData(): void
	{
		$Obj = new class() extends Struct
		{
			public $field1 = 111;
			public $field2 = 'abc';
			public $field3 = null;
		};

		$hydrated = $Obj->hydrate([]);

		$this->assertTrue($hydrated);
		$this->assertSame(111, $Obj->field1);
		$this->assertSame('abc', $Obj->field2);
		$this->assertSame(null, $Obj->field3);
	}

	public function testBasicHydrationExtraData()
	{
		$hydrate_data = [
			'field1' => 'test1',
			'field2' => 2,
			'field3' => 12.43,
			'field4' => false,
		];

		$Obj = new class() extends Struct
		{
			public $field1;
			public $field2;
		};
		$hydrated = $Obj->hydrate($hydrate_data);

		$this->assertTrue($hydrated);
		$this->assertSame($hydrate_data['field1'], $Obj->field1);
		$this->assertSame($hydrate_data['field2'], $Obj->field2);
	}

	public function testHydrationClassSettingsHydrationOff()
	{
		$hydrate_data = [
			'field1' => 'new value 1',
		];

		$Obj = new #[HydrationSettings(hydrate: false)]class() extends Struct
		{
			#[HydrationSettings(hydrate: true)]
			public $field1 = 'old value 1';
		};

		$hydrated = $Obj->hydrate($hydrate_data);

		$this->assertFalse($hydrated);
		$this->assertSame('old value 1', $Obj->field1);
	}

	public function testHydrationPropertySettings()
	{
		$hydrate_data = [
			'field1' => 'new value 1',
			'field2' => 'new value 2',
		];

		$Obj = new class() extends Struct
		{
			#[HydrationSettings(hydrate: false)]
			public $field1 = 'old value 1';

			public $field2 = 'old value 2';
		};

		$hydrated = $Obj->hydrate($hydrate_data);

		$this->assertTrue($hydrated);
		$this->assertSame('old value 1', $Obj->field1);
		$this->assertSame($hydrate_data['field2'], $Obj->field2);
	}
}