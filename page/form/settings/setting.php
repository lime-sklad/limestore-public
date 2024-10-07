<?php

include $_SERVER['DOCUMENT_ROOT'].'/start.php';

echo $Render->view('/component/stories/stories.twig', [
]);