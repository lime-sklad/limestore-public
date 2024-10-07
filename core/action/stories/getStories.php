<?php

use Core\Classes\Utils\Stories;
use Core\Classes\Utils\Utils;
$contentData = Stories::getStories();

$tpl = $Render->view('/component/include_component.twig', [
    'renderComponent' => [
        '/component/stories/stories-row.twig' => [
            'contentData' => $contentData
        ]
    ]
]);

Utils::abort([
    'res' => $tpl
]);
