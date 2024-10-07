<?php

use Core\Classes\Utils\Utils;

header('Content-type: Application/json');

//удалить товар
if(isset($_POST['id']) && !empty($_POST['id'])) {

	$cateogry_id = $_POST['id'];

	$category->deleteCategory($cateogry_id);

	echo Utils::abort([
		'type'	=> 'success',
		'text' => 'ok'
	]);	
} else {
	echo Utils::abort([
		'type'	=> 'error',
		'text' => 'Cant find id'
	]);
}
