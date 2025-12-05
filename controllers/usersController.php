<?php

session_start();
function usersController(string $page, PDO $pdo): string
{
    switch ($page) {
        case 'author':
            // авторизация
            $data = [
            'login' => '',
            'password' => '',
            'role_id' => '',
            'error' => '',
            'error_type' => ''
            ];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if(isset($_POST['login'])) {
                    $_SESSION['login'] = htmlspecialchars(trim($_POST['login']));
                }
                if(isset($_POST['password'])) {
                    $_SESSION['password'] = htmlspecialchars(trim($_POST['password']));
                }

                $login = $_SESSION['login'];
                $password = $_SESSION['password'];

                $role_id = (int)checkUsers(
                    pdo: $pdo,
                    sql: SQL_USERS_ALL,
                    log: $login,
                    pass: $password
                );

                $_SESSION['role_id'] = $role_id;

                if ($role_id === 1 || $role_id === 2) {
                    $data['error'] = 'Авторизация прошла успешно';
                    $data['error_type'] = 'success';
                        if ($role_id === 1) {
                            header('Location: ?page=admin');
                            exit;
                        }
                    header('Location: ?page=posts');
                    exit;
                }
                    elseif ('Такой пользователь не зарегистрирован') {
                    $data['error'] = 'Пройдите регистрацию';
                    $data['error_type'] = 'error';
                    header('Location: ?page=main');
                    exit;
                }
            }

            $title = 'authorTitle';

            $pageTitleData = getDataDB($pdo, TITLE, [$title]);

            $pageTitle = $pageTitleData[0]['pageTitle'] ?? 'Форма';

            $authForm = renderTemplate('author', $data);

            $content = renderTemplate('author', [
                'pageTitle' => $pageTitle,
                'author' => $authForm
            ]);

            $menu = menuController($pdo, MAIN_MENU, MAIN_LINKS);

            return renderTemplate('main', [
                'menu' => $menu,
                'content' => $content
            ]);

        case 'theme':
            $login = $_SESSION['login'];

            $page = $_GET['page'] ?? '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['radio'])) {

                $theme = $_POST['radio'];

                if ($theme === 'backgroundColorLigth' || $theme === 'backgroundColorDark') {
                    $_SESSION['theme'] = $theme;
                }
            }

            $theme = 'theme';

            $pageTitleData = getDataDB($pdo, TITLE, [$theme]);

            $pageTitle = $pageTitleData[0]['pageTitle'] ?? 'Тема';

            $content = renderTemplate('theme', [
                'login' => $login,
                'pageTitle' => $pageTitle
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

        default:

            http_response_code(400);

            return "Нет такой страницы";

    }
}
