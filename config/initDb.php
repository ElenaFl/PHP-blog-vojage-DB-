<?php
$host = 'localhost';
$dbname = 'voyage';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    // echo "Подключение к БД успешно!<br>";
} catch (PDOException $e) {
    die("<h1>Ошибка БД: " . htmlspecialchars($e->getMessage()) . "</h1>");
}
