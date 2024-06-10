<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\NotEmpty;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\Settings\ValidatorFailureMessage;
use TodoMakeUsername\DataProcessingStruct\Struct;
use TodoMakeUsername\DataProcessingStruct\Validator\Exceptions\ValidatorException;

/**
 * This one uses notEmpty to test since it is simple.
 */
class ValidatorFailureMessageTest extends TestCase
{
	public function testValidatorFailWithMessage(): void
	{
		$Obj = new class() extends Struct
		{
			#[NotEmpty]
			#[ValidatorFailureMessage(attribute_class: NotEmpty::class, message: 'My fail message')]
			public $prop1;
		};

		$Response = $Obj->validate();
		$this->assertFalse($Response->success);

		$this->assertSame('My fail message', $Response->property_responses['prop1']->messages[0]);
	}

	public function testValidatorMultipleFailWithMultipleMessages(): void
	{
		$Obj = new class() extends Struct
		{
			#[NotEmpty]
			#[ValidatorFailureMessage(attribute_class: NotEmpty::class, message: 'prop1 fail message')]
			public $prop1;

			#[NotEmpty]
			#[ValidatorFailureMessage(NotEmpty::class, 'prop2 fail message')]
			public $prop2;
		};

		$Response = $Obj->validate();
		$this->assertFalse($Response->success);

		$messages = $Response->property_responses;
		$this->assertTrue(in_array('prop1 fail message', $messages['prop1']->messages));
		$this->assertTrue(in_array('prop2 fail message', $messages['prop2']->messages));
	}

	public function testValidatorBadAttributeClass(): void
	{
		$Obj = new class() extends Struct
		{
			#[NotEmpty]
			#[ValidatorFailureMessage(attribute_class: DateTime::class, message: 'LOL wut')]
			public $prop1;
		};

		$this->expectException(ValidatorException::class);
		$this->expectExceptionMessage("'DateTime' must extend the AbstractValidatorAttribute class to be used with ValidatorFailureMessage");

		$Response = $Obj->validate();

		$this->fail('This was supposed to throw an exception');
	}
}