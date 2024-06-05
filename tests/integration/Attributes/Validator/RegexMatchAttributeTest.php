<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\RegexMatch;
use TodoMakeUsername\DataProcessingStruct\Struct;
use TodoMakeUsername\DataProcessingStruct\Validator\Exceptions\ValidatorException;

class RegexMatchAttributeTest extends TestCase
{
	public function testRegexValidateTrue(): void
	{
		$Obj = new class() extends Struct
		{
			#[RegexMatch(pattern: '/^[A-Z]+$/')]
			public $prop1 = 'ABC';
		};

		$Response = $Obj->validate();
		$this->assertTrue($Response->success);
	}

	public function testRegexValidateFalse(): void
	{
		$Obj = new class() extends Struct
		{
			#[RegexMatch(pattern: '/^[A-Z]+$/')]
			public $prop1 = 'abc';
		};

		$Response = $Obj->validate();
		$this->assertFalse($Response->success);
	}

	public function testRegexValidateFalseUninitializedNoType(): void
	{
		$Obj = new class() extends Struct
		{
			#[RegexMatch(pattern: '/^[A-Z]+$/')]
			public $prop1;
		};

		$Response = $Obj->validate();
		$this->assertFalse($Response->success);
	}

	public function testRegexValidateFalseUninitializedTyped(): void
	{
		$Obj = new class() extends Struct
		{
			#[RegexMatch(pattern: '/^[01]+$/')]
			public int $prop1;
		};

		$Response = $Obj->validate();
		$this->assertFalse($Response->success);
	}

	public function testRegexValidateIntPatternTrue(): void
	{
		$Obj = new class() extends Struct
		{
			#[RegexMatch(pattern: '/^[10]+$/')] // Binary numbers only
			public int $prop1 = 10010010101;
		};

		$Response = $Obj->validate();
		$this->assertTrue($Response->success);
	}

	public function testRegexValidateInvalidPattern(): void
	{
		$Obj = new class() extends Struct
		{
			#[RegexMatch(pattern: '/^([0-9]++/')]
			public $prop1 = '0000000000000';
		};

		$this->expectException(ValidatorException::class);
		$this->expectExceptionMessage("Invalid pattern used to validate 'prop1': '/^([0-9]++/'");

		$Response = $Obj->validate();

		$this->fail('This was supposed to throw an exception');
	}

	public function testRegexValidateNotStringCompatible(): void
	{
		$Obj = new class() extends Struct
		{
			#[RegexMatch(pattern: '/^[A-Z]+$/')]
			public $prop1;
		};

		$Obj->prop1 = (new class() {});

		$Response = $Obj->validate();
		$this->assertFalse($Response->success);
	}
}