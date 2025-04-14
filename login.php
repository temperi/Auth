<?php
session_start();
require_once 'includes/db.php';

// Генерация и проверка CSRF-токена
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка CSRF-токена
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: index.html?message=Ошибка: недействительный CSRF-токен.&type=error');
        exit;
    }

    // Ограничение попыток входа
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt'] = time();
    }

    if ($_SESSION['login_attempts'] >= 5 && (time() - $_SESSION['last_attempt']) < 300) {
        header('Location: index.html?message=Слишком много попыток входа. Подождите 5 минут.&type=error');
        exit;
    }

    $email    = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.html?message=Некорректный email.&type=error');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['login_attempts'] = 0; // Сброс попыток
        header('Location: index.html?message=Добро пожаловать, ' . htmlspecialchars($user['name']) . '!&type=success');
    } else {
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt'] = time();
        header('Location: index.html?message=Неверный email или пароль.&type=error');
    }
}
?>

<?php
// Функция для генерации CSRF-токена (для использования в index.html)
function generateCsrfToken() {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
?>