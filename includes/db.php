<?php
$host = 'localhost';
$db   = 'temperi_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // В продакшене не показываем детали ошибки
    if (defined('ENV') && ENV === 'production') {
        error_log('Database connection error: ' . $e->getMessage());
        die('Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.');
    } else {
        die('Ошибка подключения: ' . $e->getMessage());
    }
}
?>