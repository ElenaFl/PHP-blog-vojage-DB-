<?php

session_start(); // Запуск сессии — необходимо для flash‑сообщений
function postsController(string $page, PDO $pdo): string
{
    switch($page) {
        case 'posts':
            $login = $_SESSION['login'];

            $title = 'postsTitle';

            $pageTitleData = getDataDB($pdo, TITLE, [$title]);

            $pageTitle = $pageTitleData[0]['pageTitle'] ?? 'Посты';

            $posts = getDataDB($pdo, SQL_POSTS_ALL);

            // КАТЕГОРИИ В ФОРМАТЕ [id => name]
            $categories = getCategoriesForSelect($pdo, SQL_CATEGORIES_ALL);

            $contentPosts = renderTemplate('posts', [
                'pageTitle' => $pageTitle,
                'login' => $login,
                'posts' => $posts,
                'categories' => $categories
            ]);

            if ($_SESSION['role_id'] === 2) {
                $menu = menuController($pdo, BIG_MENU, BIG_LINKS);
            } elseif ($_SESSION['role_id'] === 1) {
                $menu = menuController($pdo, MENU_ADMIN, ADMIN_LINKS);
            }

            return renderTemplate('main', [
                'menu' => $menu,
                'content' => $contentPosts
            ]);

        case 'post':
            // Отображение поста по id
            $id = $_GET['id'] ?? null; // id из URL

            $login = $_SESSION['login'];

            if ($id === null) {
                http_response_code(400); // 400 Bad Request — ID не указан
                die('id не указан');
            }

            $post = getInnerDataDB($pdo, SQL_POST_OF_ID, [$id]);

            $categories = getCategoriesForSelect($pdo, SQL_CATEGORIES_ALL);

            $comments = getDataDB($pdo, SQL_COMMENTS_POST, [$id]);

            $content = renderTemplate('post', [
                'login' => $login,
                'data' => $post,
                'comments' => $comments,
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

        case 'create':
            $login = $_SESSION['login'];

            $formData = [
                'title'      => '',
                'text'       => '',
                'image'      => '',
                'errors'     => [],
                'message'    => '',
                'message_type' => '',
                'category_id' => null
            ];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $inputData = [
                    'title'       => trim(htmlspecialchars($_POST['title'] ?? '')),
                    'category_id' => (int)($_POST['category_id'] ?? 0),
                    'text'        => trim(htmlspecialchars($_POST['text'] ?? '')),
                ];
                $inputData['image'] = !empty($inputData['image']) ? $inputData['image'] : null;

                // Загружаем изображение (если есть)
                $result = handleImageUpload($inputData, $formData);
                $inputData = $result['inputData'];
                $formData = $result['formData'];

                $validationErrors = validatePostData($inputData);

                $formData['category_id'] = $inputData['category_id'];

                if (empty($validationErrors)) {
                    //  Сохраняем в БД
                    try {
                        $stmt = $pdo->prepare(
                            "INSERT INTO posts
                            (title, text, image, category_id, user_id, created_at, updated_at)
                            VALUES (?, ?, ?, ?, ?, NOW(), NOW())"
                        );
                        $stmt->execute([
                            $inputData['title'],
                            $inputData['text'],
                            $inputData['image'],
                            $inputData['category_id'],
                            $_SESSION['user_id'],
                        ]);

                        $formData['message'] = 'Пост успешно создан';
                        $formData['message_type'] = 'success';

                    } catch (PDOException $e) {
                        error_log('DB Error: ' . $e->getMessage());
                        $formData['message'] = 'Ошибка БД: ' . $e->getMessage();
                        $formData['message_type'] = 'error';
                    }
                } else {
                    // Если есть ошибки валидации
                    $formData['errors'] = $validationErrors;
                    $formData['message'] = 'Исправьте ошибки в форме';
                    $formData['message_type'] = 'error';

                    // Сохраняем введённые данные
                    $formData['title'] = $inputData['title'];
                    $formData['text'] = $inputData['text'];
                    $formData['category_id'] = $inputData['category_id'];
                }
            }

            $titlePage = 'postCraeteTitle';
            $postCreateTitle = getDataDB($pdo, TITLE, [$titlePage]);
            $pageTitle = !empty($postCreateTitle)
                ? $postCreateTitle[0]['pageTitle']
                : 'Создать пост';

            $categories = getCategoriesForSelect($pdo, SQL_CATEGORIES_ALL);

            $content = renderTemplate('postCreate', [
                'pageTitle' => $pageTitle,
                'login' => $login,
                ...$formData,
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

        case 'edit':
            $login = $_SESSION['login'];
            // ID поста из URL
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                $_SESSION['flash_message'] = 'Не указан ID поста';
                $_SESSION['flash_message_type'] = 'error';
                header('Location: ?page=posts');
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
                    header('Location: ?page=post&id=' . $id);
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
                    header('Location: ?page=posts');
                    exit;
                }
                $data['post'] = $post;
                $data['image'] = $post['image'] ?? '';
                $data['title'] = htmlspecialchars($post['title']);
                $data['category_id'] = (int)$post['category_id'];
                $data['text'] = htmlspecialchars($post['text']);
            }

            $categories = getCategoriesForSelect($pdo, SQL_CATEGORIES_ALL);

            $content = renderTemplate('edit', [
                'login' => $login,
                ...$data,
                'categories' => $categories
            ]);

            $menu = menuController($pdo, BIG_MENU, BIG_LINKS);

            return renderTemplate('main', [
                'menu' => $menu,
                'content' => $content
            ]);


        case 'delete':
            $id = $_GET['id'] ?? null;
            if (!$id || !is_numeric($id)) {
                http_response_code(400);
                return 'Некорректный ID поста';
            }
            $id = (int)$id;

            if (deletePost($pdo, $id)) {
                $_SESSION['flash_message'] = "Пост удалён успешно";
                $_SESSION['flash_message_type'] = "success";
                header('Location: ?page=posts');
                exit;
            } else {
                http_response_code(500);
                return 'Ошибка при удалении поста из БД';
            }

        case 'likes':
            $id = $_GET['id'] ?? null;
            if ($id === null) {
                http_response_code(400);
                echo json_encode(['error' => 'ID поста не указан']);
                exit;
            }

            header('Content-Type: application/json');
            addLike($id);
            break;

        default:
            // Обработка несуществующих страниц
            http_response_code(400); // 400 "Bad Request"
            return "Нет такой страницы";
    }
}






