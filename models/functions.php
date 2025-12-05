<?php

//Получение данных в виде общего массива (массив массивов) в соответствии с запросом
function getDataDB(PDO $pdo, string $sql, array $params =[]): array
{
   try{
        // подготовка SQL-запроса к исполнению ($stmt - объект класса PDOStatement, который
        // хранит подготовленный шаблон запроса)
        $stmt = $pdo->prepare($sql);

        if(!$stmt) {
            error_log("Ошибка подготовки SQL-запроса: $sql");
            return [];
        }

        // метод $stmt execute() запускает выполнение подготовленного запроса, а также
        // подставляет значение параметров подготовленного запроса (если они есть)
        // (СУБД читает данные из БД и возвращает результат PHP-скрипту)
        $stmt->execute($params);

        //получение результатов в виде ассоциативного массива (все строки сразу), где ключи -
        // названия столбцов
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;

    } catch (PDOException $e) {

        error_log("SQL Error: " . $e->getMessage());

        return [];
    }
}

function getPostById(PDO $pdo, string $sqlSelectId, int $id): ?array
{
    try {
        $stmt = $pdo->prepare($sqlSelectId);
        $stmt->execute([$id]);

        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        return $post ?: null; // Возвращаем массив или null, если пост не найден
    } catch (PDOException $e) {
        error_log('Ошибка при получении поста по ID: ' . $e->getMessage());
        return null;
    }
}

// Значение первого элемента вложенного массива
function getInnerDataDB(PDO $pdo, string $sql, array $params = []): array
{
    try {
        $data = getDataDB($pdo, $sql, $params); // Убрали = []

        // Если данные есть — возвращаем первую строку
        if (!empty($data)) {
            return $data[0];
        }
        // Если данных нет — возвращаем пустой массив
        return [];

    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage());
        return [];
    }
}

function getCategoriesForSelect(PDO $pdo, string $sql, array $params = []): array
{
    $data = getDataDB($pdo, $sql, []);
    
    $result = [];
    foreach ($data as $row) {
        $result[(int)$row['id']] = (string)$row['name'];
    }
    
    return $result;
}

// Проверка данных
function validatePostData(array $data): array
{
    $errors = [];

    // Валидация заголовка
    $title = trim($data['title'] ?? '');
    if ($title === '') {
        $errors['title'] = 'Заголовок обязателен';
    } else {
        $len = mb_strlen($title, 'UTF-8');
        if ($len < 3) {
            $errors['title'] = 'Заголовок слишком короткий (минимум 3 символа)';
        } elseif ($len > 100) {
            $errors['title'] = 'Заголовок слишком длинный (максимум 100 символов)';
        }

        // Проверка повтора символов
        if (preg_match('/(.)\\1{5,}/u', $title)) {
            $errors['title'] = 'Избегайте повтора символов в заголовке';
        }
    }

    // Валидация категории
    if (empty($data['category_id']) || $data['category_id'] <= 0) {
        $errors['category_id'] = 'Выберите маршрут';
    }

    // Валидация текста
    $text = trim($data['text'] ?? '');
    if ($text === '') {
        $errors['text'] = 'Содержание обязательно';
    } else {
        $len = mb_strlen($text, 'UTF-8');
        if ($len < 10) {
            $errors['text'] = 'Текст слишком короткий (минимум 10 символов)';
        } elseif ($len > 5000) {
            $errors['text'] = 'Текст слишком длинный (максимум 5000 символов)';
        }

        // Проверка повтора символов
        if (preg_match('/(.)\\1{10,}/u', $text)) {
            $errors['text'] = 'Избегайте повтора символов в тексте';
        }
    }

    // Проверка запрещённых слов (только если нет критических ошибок)
    if (empty($errors['title']) && empty($errors['text'])) {
        $bannedWords = [
            'мат', 'оскорбление', 'спам', 'viagra', 'casino', 'loan',
            'free money', 'click here', 'xxx', 'porn', 'грабь', 'убивай',
            'взлом', 'ключ', 'аккаунт', 'бесплатно', 'выигрыш', 'приз'
        ];

        $combinedText = strtolower(
            ($title !== '' ? $title . ' ' : '') .
            ($text !== '' ? $text : '')
        );

        foreach ($bannedWords as $word) {
            if (stripos($combinedText, $word) !== false) {
                if (stripos(strtolower($title), $word) !== false) {
                    $errors['title'] = 'В заголовке обнаружено запрещённое слово: «' . htmlspecialchars($word) . '»';
                }
                if (stripos(strtolower($text), $word) !== false) {
                    $errors['text'] = 'В тексте обнаружено запрещённое слово: «' . htmlspecialchars($word) . '»';
                }
                break;
            }
        }
    }
    return $errors;
}

// Редактирование изображения
function uploadPostImage(array $inputData, array $postData): array|false
{
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        if (isset($postData['image'])) {
            $inputData['image'] = $postData['image'];
        }
        return $inputData;
    }

    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024;

    if (!in_array($file['type'], $allowedTypes)) {
        $postData['errors']['image'] = 'Допустимы только JPG, PNG или GIF';
        return false;
    }

    if ($file['size'] > $maxSize) {
        $postData['errors']['image'] = 'Файл слишком большой (макс. 2 МБ)';
        return false;
    }

    $newFileName = uniqid() . '_' . basename($file['name']);
    $destinationPath = UPLOAD_DIR . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $destinationPath)) {
        $inputData['image'] = $newFileName;
        return $inputData;
    } else {
        $postData['errors']['image'] = 'Ошибка при загрузке файла';
        return false;
    }
}

//Добавление нового поста в БД
function handleCreatePost($pdo) {
    // Устанавливаем заголовок для JSON-ответа
    header('Content-Type: application/json; charset=utf-8');

    // Проверка обязательных полей
    if (empty($_POST['title']) || empty($_POST['category_id']) || empty($_POST['text'])) {
        return json_encode([
            'error' => 'Заполните все поля'
        ], JSON_UNESCAPED_UNICODE);
    }

    // Загрузка изображения (если есть)
    $imagePath = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        // Проверяем MIME-тип
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            return json_encode([
                'error' => 'Допустимы только JPG, PNG или GIF'
            ], JSON_UNESCAPED_UNICODE);
        }

        // Проверяем размер файла (максимум 2 МБ)
        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            return json_encode([
                'error' => 'Файл слишком большой (макс. 2 МБ)'
            ], JSON_UNESCAPED_UNICODE);
        }

        $uploadDir = __DIR__ . '/../uploads/posts/';
        // Очищаем имя файла
        $safeFileName = preg_replace('/[^\w\d\-\.]/', '_', basename($_FILES['image']['name']));
        $fileName = uniqid() . '_' . $safeFileName;
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            $imagePath = '/uploads/posts/' . $fileName;
        } else {
            return json_encode([
                'error' => 'Ошибка загрузки изображения'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    // Сохранение в БД
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO posts
                (title, text, image, category_id, user_id, created_at, updated_at)
            VALUES
                (?, ?, ?, ?, ?, NOW(), NOW())"
        );
        $stmt->execute([
            $_POST['title'],
            $_POST['text'],
            $imagePath,
            $_POST['category_id'],
            $_SESSION['user_id']
        ]);

        return json_encode([
            'success' => true
        ], JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {
        error_log('DB Error in handleCreatePost: ' . $e->getMessage());
        return json_encode([
            'error' => 'Ошибка БД: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

// Загрузка изображения
function handleImageUpload(array $inputData, array $formData, ?array $existingData = null): array {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/posts/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2 МБ

        $file = $_FILES['image'];
        $fileName = basename($file['name']);
        $fileType = $file['type'];
        $fileSize = $file['size'];
        $fileTmpPath = $file['tmp_name'];

        if (!in_array($fileType, $allowedTypes)) {
            $formData['errors']['image'] = 'Допустимы только JPG, PNG или GIF';
        } elseif ($fileSize > $maxSize) {
            $formData['errors']['image'] = 'Файл слишком большой (макс. 2 МБ)';
        } else {
            $newFileName = uniqid() . '_' . $fileName;
            $destinationPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                $inputData['image'] = $newFileName;
            } else {
                $formData['errors']['image'] = 'Ошибка при загрузке файла';
            }
        }
    }
    // Если файл не загружен, но в посте уже было изображение — оставляем старое
    elseif ($existingData && isset($existingData['image'])) {
        $inputData['image'] = $existingData['image'];
    }

    return [
        'inputData' => $inputData,
        'formData'  => $formData
    ];
}

function editPost(PDO $pdo, string $table, string $sqlGetPostByIdCreatedAt, string $sqlUpdatePost,
    array $post, string $id, ?string $imagePath = null): bool {
    try {
        // Получаем текущий пост из БД
        $stmt = $pdo->prepare($sqlGetPostByIdCreatedAt);
        $stmt->execute([$id]);
        $existingPost = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingPost) {
            error_log("Пост с ID {$id} не найден в таблице {$table}");
            return false;
        }

        // Формируем данные для обновления
        $updatedPost = [
            'title' => $post['title'] ?? '',
            'category_id' => $post['category_id'] ?? null,
            'text' => $post['text'] ?? '',
            'updated_at' => date('Y-m-d H:i:s')           // обновляем текущую дату
        ];
        // Подготавливаем запрос на обновление
        $stmt = $pdo->prepare($sqlUpdatePost);

        // Параметры для SQL
        $params = [
            ':title' => $updatedPost['title'],        // title
            ':image' => $imagePath,                  // image
            ':category_id' => $updatedPost['category_id'], // category_id
            ':text' => $updatedPost['text'],         // text
            ':updated_at' => $updatedPost['updated_at'],   // updated_at
            ':id' => $id                          // id 
        ];

        $result = $stmt->execute($params);

        if ($result) {
            error_log("Пост ID {$id} успешно обновлён в таблице {$table}");
            return true;
        } else {
            error_log("Ошибка при обновлении поста ID {$id} в таблице {$table}");
            return false;
        }

    } catch (PDOException $e) {
        error_log('Ошибка БД при редактировании поста: ' . $e->getMessage());
        return false;
    } catch (Exception $e) {
        error_log('Неожиданная ошибка при редактировании поста: ' . $e->getMessage());
        return false;
    }
}

function deletePost(PDO $pdo, int $id): bool
{
    try {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result && $stmt->rowCount() > 0) {
            error_log("Пост ID {$id} удалён из БД");
            return true;
        } else {
            error_log("Пост ID {$id} не найден или не удалён");
            return false;
        }
    } catch (PDOException $e) {
        error_log("Ошибка при удалении поста: " . $e->getMessage());
        return false;
    }
}


function addLike(string $id): void
{
    // Инициализируем массив лайков, если его нет
    if (!isset($_SESSION['post_likes'])) {
        $_SESSION['post_likes'] = [];
    }

    // Увеличиваем лайки для конкретного поста
    $_SESSION['post_likes'][$id] = ($_SESSION['post_likes'][$id] ?? 0) + 1;

    // Возвращаем только лайки текущего поста
    header('Content-Type: application/json');
    echo json_encode(['likes' => $_SESSION['post_likes'][$id]]);
    exit;
}

function checkUsers(PDO $pdo, string $sql, string $log, string $pass, array $params = []): string
{
    $users = getDataDB($pdo,  $sql, $params);

    foreach ($users as $user) {
        if ($user['login'] === $log && $user['password'] === $pass) {
            return $user['role_id'];
        }
    }
    return 'Такой пользователь не зарегистрирован';
}

//Калькулятор
function addition(float $arg1, float $arg2): float
{
    return $arg1 + $arg2;
}

function subtraction (float $arg1, float $arg2): float
{
    return $arg1 - $arg2;
}

function multiply(float $arg1, float $arg2): float
{
    return $arg1 * $arg2;
}

function division(float $arg1, float $arg2): float|string
{
    return $arg2 === 0.0 ? 'На ноль делить нельзя!' : $arg1 / $arg2;
}


function calculate(float $arg1, float $arg2, string $operator): float|string
{
    return match ($operator) {
        '+' => addition($arg1, $arg2),
        '-' => subtraction($arg1, $arg2),
        '*' => multiply($arg1, $arg2),
        '/' => division($arg1, $arg2),
        default => 'Ошибка!'
    };
}




