<?php
function mainController(PDO $pdo): string
{
    $menu = menuController($pdo, MAIN_MENU, MAIN_LINKS);

    $mt = 'mainTitle';

    $content = titleController($pdo, TITLE, [$mt]);
    

    return renderTemplate('mainFon', [
        'menu' => $menu,
        'content' => $content
    ]);
}