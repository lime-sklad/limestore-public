<?php 

header('Content-type: Application/json');

if(!empty($_POST['id'])) {
    $productsFilter->deleteFilter($_POST['id']);
    
    echo $utils::abort([
        'type' => 'success',
        'text' => 'ok'
    ]);

    return;
}

echo $utils::errorAbort('cant find id');

