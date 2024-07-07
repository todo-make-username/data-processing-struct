<?php declare(strict_types=1);

use TodoMakeUsername\DataProcessingStructDemo\Util\ObjectFactory;

$Obj             = ObjectFactory::create($_POST['section']);
$display_message = 'Success!';
$serialized_obj  = [];

unset($_POST['section']);

try {
	$Obj->hydrate($_POST);
	$Response = $Obj->validate();

	if ($Response->success === false)
	{
		$failure_messages = $Response->getAllMessages();
		$display_message  = implode(PHP_EOL, $failure_messages);
	}

	$serialized_obj = $Obj->toArray();
} catch (\Throwable $e) {
	$display_message = $e->getMessage();
}

$response = [
	'message'    => $display_message,
	'post'       => $_POST,
	'files'      => $_FILES,
	'serialized' => $serialized_obj,
];

echo json_encode($response);