<?php
	header('Content-type: Application/json');
	
	require $_SERVER['DOCUMENT_ROOT'].'/start.php';
	require $_SERVER['DOCUMENT_ROOT'].'/core/main/dbMigration/oldVersionMirgration.php';

	if(!empty($_POST['login']) && !empty($_POST['password'])) {
		$login = $_POST['login'];
		$password = $_POST['password'];

		return $System->auth($login, $password);
	} else {
		echo $utils::abort([
			'error' => 'Заполните все поля'
		]);
	}
