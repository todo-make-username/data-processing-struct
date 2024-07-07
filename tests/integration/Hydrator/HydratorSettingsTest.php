<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Hydrator\Settings\HydrationSettings;
use TodoMakeUsername\DataProcessingStruct\Struct;

class HydratorSettingsTest extends TestCase
{
	public function testHydrationClassSettingsHydrationOff(): void
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

	public function testHydrationPropertySettings(): void
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

	public function testConversionSettingOff(): void
	{
		$hydrate_data = [
			'from_string' => '',
		];

		$Obj = new class() extends Struct
		{
			#[HydrationSettings(enhanced_conversion: false)]
			public array $from_string;
		};

		$this->expectException(TypeError::class);
		$this->expectExceptionMessageMatches('/^Cannot assign string to property .*?::\$from_string of type array$/');

		$Obj->hydrate($hydrate_data);

		$this->fail('This should have thrown an exception');
	}
}