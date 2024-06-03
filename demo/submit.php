<?php declare(strict_types=1);

use TodoMakeUsername\DataProcessingStructDemo\Util\ObjectFactory;

$Obj            = ObjectFactory::create($_POST['section']);
$NewObj         = null;
$message        = 'Success!';
$serialized_obj = [];

unset($_POST['section']);

if (is_null($Obj) === false)
{
	try {
		$Obj->hydrate($_POST);
		$serialized_obj = $Obj->toArray();
	} catch (\Throwable $e) {
		$message = $e->getMessage();
	}
}

$response = [
	'message'    => $message,
	'post'       => $_POST,
	'files'      => $_FILES,
	'serialized' => $serialized_obj,
];

echo json_encode($response);