<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка CSRF-токена
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: index.html?message=Ошибка: недействительный CSRF-токен.&type=error');
        exit;
    }

    $name     = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email    = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.html?message=Некорректный email.&type=error');
        exit;
    }

    if (strlen($name) < 2) {
        header('Location: index.html?message=Имя должно содержать не менее 2 символов.&type=error');
        exit;
    }

    // Проверка на существующий email
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        header('Location: index.html?message=Email уже зарегистрирован.&type=error');
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$name, $email, $password]);
        header('Location: index.html?message=Регистрация прошла успешно!&type=success');
    } catch (PDOException $e) {
        header('Location: index.html?message=Ошибка: ' . htmlspecialchars($e->getMessage()) . '&type=error');
    }
}
?>