<?php

use Core\Classes\Utils\Utils;

header('Content-type', 'Application/json');

$report = new \Core\Classes\Report;

if(!empty($_POST['report_id'])) {
	$report->deleteOrder($_POST);
	echo $utils::successAbort('Ok');
} else {
	return Utils::errorAbort('Emty result');
}


