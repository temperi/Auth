<?php
$host = 'localhost';      // Хост базы данных
$db   = 'temperi_db';     // Имя базы данных
$user = 'root';           // Пользователь базы
$pass = '';               // Пароль (оставь пустым для локального сервера)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    // Включаем отображение ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Ошибка подключения: ' . $e->getMessage();
    exit;
}
?>
