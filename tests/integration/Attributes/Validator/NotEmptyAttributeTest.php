<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\NotEmpty;
use TodoMakeUsername\DataProcessingStruct\Struct;

class NotEmptyAttributeTest extends TestCase
{
	public function testNotEmptyValidateTrue(): void
	{
		$Obj = new class() extends Struct
		{
			#[NotEmpty]
			public $prop1;
		};

		$Obj->prop1 = 'No longer empty';

		$Response = $Obj->validate();
		$this->assertTrue($Response->success);
	}

	public function testNotEmptyValidateFalse(): void
	{
		$Obj = new class() extends Struct
		{
			#[NotEmpty]
			public $prop1;
		};

		$Obj->prop1 = null;

		$Response = $Obj->validate();
		$this->assertFalse($Response->success);
	}

}