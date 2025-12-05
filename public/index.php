<?php
include_once __DIR__ .  '/../vendor/autoload.php';

// гарантированно делает доступной переменную $pdo из initDb.php
require __DIR__ . '/../config/initDb.php';


$page = $_GET['page'] ?? 'index';
// $method = $_SERVER['REQUEST_METHOD'];

// // Обрабатываем POST отдельно
// if ($method === 'POST') {
//     switch ($page) {
//         case 'create':
//             echo handleCreatePost($pdo);  
//             break;
//         case 'edit':
//             echo handleUpdatePost($pdo);
//             break;

//         default:
//             http_response_code(405);
//             echo 'Метод не разрешён';
//     }
//     exit;
// }


switch ($page) {
    case 'index':
        echo mainController($pdo);
        break;
    case 'posts':
        echo postsController($page, $pdo);
        break;
    case 'post':
        echo  postsController($page, $pdo);
        break;
    case 'create':
        echo postsController($page, $pdo);
        break;
    case 'calc':
        echo calcController($pdo);
        break;
    case 'edit':
        echo postsController($page, $pdo);
        break;
    case 'delete':
        echo postsController($page, $pdo);
        break;
    case 'likes':
        echo postsController($page, $pdo);
        break;
    case 'author':
        echo usersController($page,$pdo);
        break;
    case 'admin':
        echo adminController($page, $pdo);
        break;
    case 'editAdmin':
        echo adminController($page, $pdo);
        break;
    case 'deleteAdmin':
        echo adminController($page, $pdo);
        break;
    case 'theme':
        echo usersController($page,$pdo);
        break;
    default:
        echo mainController($pdo);
}











