<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Tailor\Trim;
use TodoMakeUsername\DataProcessingStruct\Attributes\Tailor\UseDefaultOnEmpty;
use TodoMakeUsername\DataProcessingStruct\Tailor\Exceptions\TailorException;
use TodoMakeUsername\DataProcessingStruct\Struct;

class UseDefaultOnEmptyAttributeTest extends TestCase
{
	public function testUseDefaultOnEmpty(): void
	{
		$Obj = new class() extends Struct
		{
			#[UseDefaultOnEmpty]
			public $field1 = 'default';
		};

		$Obj->hydrate([ 'field1' => '' ]);
		$Obj->tailor();

		$this->assertSame('default', $Obj->field1);
	}

	public function testTrimWithUseDefaultOnEmpty(): void
	{
		$Obj = new class() extends Struct
		{
			#[Trim]
			#[UseDefaultOnEmpty]
			public $field1 = 'default';
		};

		$Obj->hydrate([ 'field1' => '            ' ]);
		$Obj->tailor();

		$this->assertSame('default', $Obj->field1);
	}

	public function testUseDefaultOnEmptyNoDefault(): void
	{
		$Obj = new class() extends Struct
		{
			#[UseDefaultOnEmpty]
			public string $field1;
		};

		$this->expectException(TailorException::class);
		$this->expectExceptionMessage('The property: "field1" must have a default value for the DefaultOnEmpty attribute.');

		$Obj->hydrate([ 'field1' => '' ]);
		$Obj->tailor();

		$this->fail('This was supposed to throw an exception');
	}
}