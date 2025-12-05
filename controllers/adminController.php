<?php
function adminController(string $page, PDO $pdo): string
{
    switch($page) {
        case 'admin':
            $login = $_SESSION['login'];

            $menu = menuController($pdo, MENU_ADMIN, ADMIN_LINKS );

            $data = getDataDB($pdo, SQL_POSTS_ALL);

            $categories = getCategoriesForSelect($pdo, SQL_CATEGORIES_ALL);

            $comments = getDataDB($pdo, SQL_COMMENTS_ALL);

            $contentPosts = renderTemplate('postsAdmin', [
                'login' => $login,
                'comments' => $comments,
                'posts' => $data,
                'categories' => $categories
            ]);

            return renderTemplate('main', [
                'menu' => $menu,
                'content' => $contentPosts,
            ]);

        case 'editAdmin':
            $login = $_SESSION['login'];
            // ID поста из URL
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                $_SESSION['flash_message'] = 'Не указан ID поста';
                $_SESSION['flash_message_type'] = 'error';
                header('Location: ?page=postsAdmin');
                exit;
            }

            $data = [
                'id' => $_GET['id'],
                'errors' => [],
                'message' => '',
                'message_type' => '',
                'title' => '',
                'category_id' => 0,
                'text' => ''
            ];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $inputData = [
                'id' => $id,
                'title' => trim($_POST['title'] ?? ''),
                'category_id' => filter_var($_POST['category_id'] ?? 0, FILTER_VALIDATE_INT) ?? 0,
                'text' => trim($_POST['text'] ?? '')
            ];

            $result = handleImageUpload($inputData, $data, $post ?? null);
            $inputData = $result['inputData'];
            $data = $result['formData'];

            $validationErrors = validatePostData($inputData);

            if (empty($validationErrors)) {
                // путь к изображению
                if (!empty($_POST['delete_image']) && $_POST['delete_image'] === '1') {
                    $imagePath = null; // Удаление
                } elseif (isset($inputData['image'])) {
                    $imagePath = $inputData['image']; // Новый файл
                } elseif (!empty($_POST['current_image'])) {
                    $imagePath = $_POST['current_image']; // прежний путь
                } else {
                    $imagePath = null; // Нет изображения
                }

                if (editPost(
                    $pdo,
                    'posts',
                    POST_CREATED_AT_BY_ID,
                    UPDATE_POST,
                    $inputData,
                    (string)$id,
                    $imagePath
                    )) {
                    $_SESSION['flash_message'] = 'Пост отредактирован успешно';
                    $_SESSION['flash_message_type'] = 'success';
                    header('Location: ?page=admin');
                    exit;
                } else {
                    $data['message'] = 'Ошибка при редактировании';
                        $data['message_type'] = 'error';
                    }
            } else {
                $data['errors'] = array_merge($data['errors'], $validationErrors);
                $data['message'] = 'Исправьте ошибки';
                $data['message_type'] = 'error';
                $data['title'] = $inputData['title'];
                $data['category_id'] = $inputData['category_id'];
                $data['text'] = $inputData['text'];
            }
        }
        else {
                // Загрузка данных поста для отображения в форме (при GET-запросе)
                $post = getPostById($pdo, SQL_POST_OF_ID, $id);
                if (!$post) {
                    $_SESSION['flash_message'] = 'Пост не найден';
                    $_SESSION['flash_message_type'] = 'error';
                    header('Location: ?page=admin');
                    exit;
                }
                $data['post'] = $post;
                $data['image'] = $post['image'] ?? '';
                $data['title'] = htmlspecialchars($post['title']);
                $data['category_id'] = (int)$post['category_id'];
                $data['text'] = htmlspecialchars($post['text']);
            }

            $categories = getCategoriesForSelect($pdo, SQL_CATEGORIES_ALL);

            $content = renderTemplate('editAdmin', [
                'login' => $login,
                'data' => $data,
                'categories' => $categories
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

        case 'deleteAdmin' :
            $id = $_GET['id'] ?? null;
            if (!$id || !is_numeric($id)) {
                http_response_code(400);
                return 'Некорректный ID поста';
            }
            $id = (int)$id;

            if (deletePost($pdo, $id)) {
                $_SESSION['flash_message'] = "Пост удалён успешно";
                $_SESSION['flash_message_type'] = "success";
                header('Location: ?page=admin');
                exit;
            } else {
                http_response_code(500);
                return 'Ошибка при удалении поста из БД';
            }

            default:

            http_response_code(400);

            return "Нет такой страницы";
    }

}