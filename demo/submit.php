<?php declare(strict_types=1);

use TodoMakeUsername\DataProcessingStructDemo\Util\ObjectFactory;

$Obj            = ObjectFactory::create($_POST['section']);
$NewObj         = null;
$message        = 'Success!';
$serialized_obj = [];

unset($_POST['section']);

try {
	$Obj->hydrate($_POST);
	$Obj->tailor();
	$Response = $Obj->validate();

	if ($Response->success === false)
	{
		$failure_messages = [];
		foreach ($Response->messages as $key => $PropertyResponse) {
			foreach($PropertyResponse->messages as $failure_message)
			{
				$failure_messages[] = $failure_message;
			}
		}

		$message = implode(PHP_EOL, $failure_messages);
	}

	$serialized_obj = $Obj->toArray();
} catch (\Throwable $e) {
	$message = $e->getMessage();
}

$response = [
	'message'    => $message,
	'post'       => $_POST,
	'files'      => $_FILES,
	'serialized' => $serialized_obj,
];

echo json_encode($response);