<?php
define('ROOT_PATH', dirname(__DIR__));
define('VIEWS_PATH', dirname(__DIR__) . '/views');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_path = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocol . '://' . $host . rtrim($script_path));


// запрос данных всех постов из таблицы 'posts' БД
const SQL_POSTS_ALL = "SELECT * FROM posts";

// данные всех users
const SQL_USERS_ALL = "SELECT * FROM users";

// все комментарии
const SQL_COMMENTS_ALL = "SELECT * FROM comments";

// комментарии к посту по post_id
const SQL_COMMENTS_POST = "SELECT * FROM comments WHERE post_id = ?";

//все категорий
const SQL_CATEGORIES_ALL = "SELECT id, name FROM categories";

// данные поста по id
const SQL_POST_OF_ID = " SELECT *  FROM posts WHERE id = ?";

//путь к изображению
const UPLOAD_DIR = 'uploads/posts/';

// получение названий ссылок главного меню
const MAIN_MENU = 'SELECT link, name FROM menu_link WHERE link IN (?, ?)';

// массив названий ссылок главного меню
const MAIN_LINKS = ['index', 'author'];

// получение названий ссылок большого меню
const BIG_MENU = 'SELECT link, name FROM menu_link WHERE link IN (?, ?, ?, ?)';

// массив названий ссылок большого меню
const BIG_LINKS = ['index', 'posts', 'calc', 'theme'];

// получение названий ссылок меню для страницы Администратора
const MENU_ADMIN = 'SELECT link, name FROM menu_link WHERE link IN (?, ?, ?, ?, ?)';

// массив названий ссылок для страницы Администратора
const ADMIN_LINKS = ['index','posts', 'calc', 'admin', 'theme'];

// получение названий ссылок меню для страницы редактирования постов Администратором
const MENU_ADMIN_EDIT = 'SELECT link, name FROM menu_link WHERE link IN (?, ?)';

// массив названий ссылок меню для страницы редактирования постов Администратором
const ADMIN_LINKS_EDIT = ['index', 'admin'];

// получение заголовка для страницы
const TITLE = 'SELECT pageTitle FROM page_title WHERE page = ?';

// сохранение поста в БД
const INSERT_POST = "INSERT INTO posts (title, text, image, created_at, updated_at, category_id, user_id) VALUES (?, ?, ?, NOW(), NOW(), ?, ?)";


// получение даты и времени создания поста по id
const POST_CREATED_AT_BY_ID = "SELECT created_at FROM posts WHERE id = ?";

// редактирование поста по id
const UPDATE_POST = "UPDATE posts SET
    title = :title,
    image = :image,
    category_id = :category_id,
    text = :text,
    updated_at = :updated_at
WHERE id = :id";




