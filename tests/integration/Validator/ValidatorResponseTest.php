<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\NotEmpty;
use TodoMakeUsername\DataProcessingStruct\Attributes\Validator\Settings\ValidatorFailureMessage;
use TodoMakeUsername\DataProcessingStruct\Struct;

class ValidatorResponseTest extends TestCase
{
	public function testValidatorResponseJsonEncode(): void
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

		$response_json = json_encode($Response);

		$this->assertStringContainsString('prop1 fail message', $response_json);
		$this->assertStringContainsString('prop2 fail message', $response_json);
	}

	public function testGetAllMessages(): void
	{
		$Obj = new class() extends Struct
		{
			#[NotEmpty]
			#[ValidatorFailureMessage(attribute_class: NotEmpty::class, message: 'prop1 fail message')]
			public $prop1;

			#[NotEmpty]
			public $prop2; // This will return the default validation failure message for NotEmpty
		};

		$Response = $Obj->validate();
		
		$this->assertFalse($Response->success);
				
		$messages = $Response->getAllMessages();
		$this->assertSame([ 'prop1 fail message', 'The "prop2" field must contain a value.' ], $messages);
	}
}