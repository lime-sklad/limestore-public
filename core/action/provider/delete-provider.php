<?php
header('Content-type: Application/json');

use Core\Classes\Utils\Utils;

//удалить товар
if(isset($_POST['id']) && !empty($_POST['id'])) {
	$provider_id = $_POST['id'];

	$provider->deleteProvider($provider_id);

	echo Utils::successAbort('Ok');
} else {
	echo Utils::errorAbort('Cant find id');
}
