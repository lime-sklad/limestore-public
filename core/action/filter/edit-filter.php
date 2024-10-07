<?php 

header('Content-type: Application/json');

if(empty($_POST) && empty($_POST['filter_id'])) {
    echo $utils::abort([
        'type' => 'Error',
        'text'  => 'Empty data'
    ]);

    return;
}

$filter_id = $_POST['filter_id'];

// изменяем название фильтра
if($_POST['filter_name']) {
    $filter_name = $_POST['filter_name'];
    
    $productsFilter->editFilterName($filter_id, $filter_name);
}

// обновляем пункты фильтра
if(!empty($_POST['option_list'])) {
    $option_list = $_POST['option_list'];
    $productsFilter->editFilterOption($option_list);
}


// удаление 
if(!empty($_POST['deleted_option_list'])) {
    $deleted_option_list = $_POST['deleted_option_list'];
    $productsFilter->deleteFilterOption($deleted_option_list);
}

//добавляем новый пункт 
if(!empty($_POST['add_new_option'])) {
    $new_option_list = $_POST['add_new_option'];

    $productsFilter->addFilterOption($new_option_list, $filter_id);
}




echo $utils::abort([
    'type' => 'success',
    'text' => 'updated'
]);