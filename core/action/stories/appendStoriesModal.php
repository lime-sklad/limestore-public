<?php
$modal = $Render->view('/component/modal/custom_modal/modal.twig', [
    'data' => [
        'component_path' => '/component/stories/stories-control.twig',
        'class_list' => 'stories-modal',
    ]
]);


$utils::abort([
    'res' => $modal
]);