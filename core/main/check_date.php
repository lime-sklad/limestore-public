<?php

use Core\Classes\Utils\Utils;

header('Content-type: Application/json');

$ntp = Utils::ntp();

$content = $Render->view('/component/error_page/correct_date.twig', []);

if($ntp) {
    if(Utils::getDateDMY() !== $ntp) {
        return Utils::abort([
            'type' => 'error',
            'content' => $content,
            'connection_status' => 'true'
        ]);
    }
} else {
    if(Utils::isCorrectLocalDate()) {
        return Utils::abort([
            'type' => 'error',
            'content' => $content,
            'connection_status' => 'false'
        ]);
    }
}

return false;