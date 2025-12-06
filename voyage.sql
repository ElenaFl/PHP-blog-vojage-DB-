-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 06 2025 г., 13:56
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `voyage`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Австралия и Океания'),
(2, 'Азия'),
(3, 'Америка'),
(4, 'Антарктида'),
(5, 'Африка'),
(6, 'Европа');

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `author` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `author`, `content`, `date`, `post_id`) VALUES
(1, 'Иван', 'Здорово побывать на океане', '2025-12-03 00:05:05', 4),
(2, 'Александр', 'Это интересный пост', '2025-12-03 00:08:28', 2),
(3, 'Арсений', 'Благодарю за информацию', '2025-12-03 00:11:45', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `comments_tags`
--

CREATE TABLE `comments_tags` (
  `comment_tag_id` int NOT NULL,
  `tag_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `comments_tags`
--

INSERT INTO `comments_tags` (`comment_tag_id`, `tag_id`) VALUES
(2, 9),
(1, 10),
(3, 10);

-- --------------------------------------------------------

--
-- Структура таблицы `menu_link`
--

CREATE TABLE `menu_link` (
  `id` int NOT NULL,
  `link` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `menu_link`
--

INSERT INTO `menu_link` (`id`, `link`, `name`) VALUES
(1, 'index', 'главная'),
(2, 'posts', 'посты'),
(3, 'calc', 'калькулятор'),
(4, 'author', 'авторизация'),
(5, 'theme', 'тема'),
(6, 'admin', 'админ панель');

-- --------------------------------------------------------

--
-- Структура таблицы `page_title`
--

CREATE TABLE `page_title` (
  `id` int NOT NULL,
  `page` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `pageTitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `page_title`
--

INSERT INTO `page_title` (`id`, `page`, `pageTitle`) VALUES
(1, 'authorTitle', 'Для авторизации заполните форму:\r\n'),
(2, 'mainTitle', 'Добро пожаловать в блог!'),
(3, 'postCreateTitle', 'Для создания поста заполните поля:'),
(4, 'postsTitle', 'Мои путешествия: истории и открытия'),
(5, 'theme', 'Выбирите цвет фона:');

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE `posts` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `likes` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `title`, `text`, `image`, `likes`, `created_at`, `updated_at`, `category_id`, `user_id`) VALUES
(1, 'Поездка в Шри-Ланку', 'Недавно мне посчастливилось побывать на удивительном острове — Шри‑Ланка. Это путешествие стало настоящим открытием: здесь гармонично сочетаются первозданная природа, богатая культура и тёплый приём местных жителей.', '6933444bec2dd_6928d8b57bdd3_b8dcca4fe39c4c1ab0de46a36df31cf3.jpg', 5, '2025-11-28 02:01:30', '2025-12-06 01:22:56', 1, 1),
(2, 'остров Маврикий', 'Маврикий — крошечный остров (65 км с севера на юг и 41 км с запада на восток), где сливаются культуры, пейзажи и настроения. Это место, где можно одновременно увидеть колониальные кварталы, индийские храмы и китайские чайна‑тауны, а в океане — «подводный водопад» и затонувшие корабли.', '6928d9762f7d4_5a9f629ae15f68b0ff4f5372eafb1140.jpg', 7, '2025-11-28 02:05:42', '2025-12-02 18:41:04', 5, 3),
(3, 'Чёрное море', 'ару дней у моря — и будто перезагрузился. Тёплая солёная вода, шум прибоя под ногами и бескрайняя синева до горизонта.\\r\\n\\r\\nУтром — туманно над водой и тишина, только чайки перекликаются. Днём — солнце, пляжный песок, который не обжигает, и короткие заплывы, чтобы остудиться. Вечером — закат в золотых и пурпурных тонах, когда море становится зеркалом для уходящего солнца.\\r\\n\\r\\nПахнет йодом и водорослями, смеются дети у кромки воды, где волны ласково накатывают на берег. В кафе на набережной — свежий хлеб, вкусный чай и разговоры ни о чём.\\r\\n\\r\\nПросто море. Просто небо. Просто счастье.', '6928dbb87a924_chayki.jpg', 8, '2025-11-28 02:15:25', '2025-11-29 20:57:20', 6, 2),
(4, 'отдых в Египте', 'а пустынные пейзажи сменяются бирюзовыми водами Красного моря. Вот несколько основных аспектов, которые стоит учесть при планировании поездки', '6928dfab2a83d_pink.jpg', 4, '2025-11-28 02:30:25', '2025-11-28 02:35:46', 5, 3),
(5, 'фСАса', 'сЫФМвымвмвмиа', '693214866a33b_6929dfbd4657f_5a9f629ae15f68b0ff4f5372eafb1140.jpg', NULL, '2025-12-04 18:48:26', '2025-12-05 02:08:54', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Администратор'),
(2, 'Гость');

-- --------------------------------------------------------

--
-- Структура таблицы `tags`
--

CREATE TABLE `tags` (
  `id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(1, 'Австралия и Океания'),
(2, 'Азия'),
(3, 'Америка'),
(4, 'Антарктида'),
(5, 'Африка'),
(6, 'Европа'),
(7, 'Море'),
(8, 'Остров'),
(9, 'Солнце'),
(10, 'Отдых'),
(11, 'Пляж'),
(12, 'Тропические цветы');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `role_id`) VALUES
(1, 'Елена', '1234', 1),
(2, 'Арсений', '5678', 2),
(3, 'Иван', '7788', 2);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments` (`post_id`);

--
-- Индексы таблицы `comments_tags`
--
ALTER TABLE `comments_tags`
  ADD PRIMARY KEY (`comment_tag_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Индексы таблицы `menu_link`
--
ALTER TABLE `menu_link`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `page_title`
--
ALTER TABLE `page_title`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_fk4` (`category_id`),
  ADD KEY `posts_fk5` (`user_id`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`password`),
  ADD KEY `users_fk3` (`role_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `menu_link`
--
ALTER TABLE `menu_link`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `page_title`
--
ALTER TABLE `page_title`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `comments_tags`
--
ALTER TABLE `comments_tags`
  ADD CONSTRAINT `comments_tags_ibfk_1` FOREIGN KEY (`comment_tag_id`) REFERENCES `comments` (`id`),
  ADD CONSTRAINT `comments_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);

--
-- Ограничения внешнего ключа таблицы `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_fk4` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `posts_fk5` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_fk3` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
