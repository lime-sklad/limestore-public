<?php
include $_SERVER['DOCUMENT_ROOT'].'/start.php';

/**
 * 
 * В этом файле будем икнлюжить файлы для миграции базы данных 
 * 
 */


//  0.015
require 'migrationFile/0.015.php';

// 0.016
require 'migrationFile/0.016.php';
