<?php


require_once 'vendor/autoload.php';

spl_autoload_register(function ($class) {
    if(basename($class) !== 'PDO') {
        // Преобразовать пространство имен и имя класса в путь к файлу
        $file = $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $class) . '.php';

        // Если файл существует, загрузить его
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

use Core\Classes\Utils\Utils;


use Core\Classes\Privates\accessManager;

use Core\Classes\System\LicenseManager;


// $loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'].'/core/template/');
// $twig = new \Twig\Environment($loader);

$System = new \Core\Classes\System\System;

$Render = new \Core\Classes\Services\RenderTemplate;

$db = new \core\classes\dbWrapper\db;

$main = new \Core\Classes\System\Main;

$accessManager = new AccessManager;

$init = new \Core\Classes\System\Init;

$utils = new Utils;

$licenseManager = new LicenseManager;

$productsFilter = new \Core\Classes\Services\ProductsFilter;

$category = new \Core\Classes\Services\Category;

$provider = new \Core\Classes\Services\Provider;

$user = new \Core\Classes\Privates\User;

$products = new \Core\Classes\Products;