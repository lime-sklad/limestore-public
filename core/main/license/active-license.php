<?php
use Core\Classes\Utils\Utils;

header('Content-type: Application/json');

if (!empty($_POST['data'])) {
    $key = $_POST['data'];

    $licenseManager->activeLicense($key, function ($res) {
        if ($res) {
            echo  Utils::abort([
                'alert_type' => 'success',
                'text' => 'Ok'
            ]);
        } else {
            echo Utils::abort([
                'alert_type' => 'error',
                'text' => 'Key is incoccert'
            ]);
        }
    });
}

