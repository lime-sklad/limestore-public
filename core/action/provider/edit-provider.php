<?php
header('Content-type: Application/json');

use Core\Classes\Utils\Utils;

/**
 * исправить массив что бы выдывал только 1 результат
 * в js дописать js функцию update_table_row 
 * добавить в function.php id к каждой строке в табице для обновления резальутатата
 */
if(!empty($_POST['prepare_data']) && count($_POST['prepare_data']) > 1) {
	$provider->editProvider($_POST['prepare_data']);
	echo Utils::successAbort('Ok');
} else {
	echo Utils::errorAbort('Вы ничего не изменили');
}
