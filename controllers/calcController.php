<?php
function calcController(PDO $pdo): string
{
    $login = $_SESSION['login'];
    $data = [
        'result'=> 0,
        'arg1' => 0,
        'arg2' => 0,
        'operator' => ''
    ];

    if (!empty($_POST)) {
        $data['arg1'] = (float)($_POST['arg1'] ?? 0);
        $data['arg2'] = (float)($_POST['arg2'] ?? 0);
        $data['operator'] = (string)($_POST['operator'] ?? '');
    }

    if ($data['operator'] !== '') {
        $data['result'] = calculate($data['arg1'], $data['arg2'], $data['operator']);
    }
        else {
            $data['result'] = 'Выберите операцию';
        }

    $content = renderTemplate('calc', [
        'login' => $login,
        ...$data
]);

    if ($_SESSION['role_id'] === 2) {
                $menu = menuController($pdo, BIG_MENU, BIG_LINKS);
            } elseif ($_SESSION['role_id'] === 1) {
                $menu = menuController($pdo, MENU_ADMIN, ADMIN_LINKS);
            }

    return renderTemplate('main', [
        'menu' => $menu,
        'content' => $content
    ]);

}