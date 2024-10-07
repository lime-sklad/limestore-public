<?php

use Core\Classes\System\System;

require $_SERVER['DOCUMENT_ROOT'].'/start.php';

if(!isset($_SESSION['user']))
{
	// header("Location: index.php");
}

else if(isset($_SESSION['user'])!="")
{
	// header("Location: index.php");
}

if(isset($_GET['logout']))
{
	System::logout(function($callback) {
		$callback ?: header("Location: /");
	});
}
