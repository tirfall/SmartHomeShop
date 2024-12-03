<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Стартуем сессию только если она еще не была начата
}
?>

<link rel="stylesheet" href="style.css">
<header>
    <h1>Nutikad vidinad koju</h1>
    <nav>
        <a href="catalog.php">Kataloog</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Если пользователь авторизован, показываем ссылку на информацию о пользователе и кнопку выхода -->
            <a href="kasutaja.php">Kasutaja info</a>
            <a href="logout.php">Logi välja</a> <!-- Ссылка для выхода -->
            <?php if ($_SESSION['username'] == 'admin'): ?>
                <!-- Если пользователь является администратором, показываем ссылку на админ-страницу -->
                <a href="admin.php">Admin paneel</a>
            <?php endif; ?>
        <?php else: ?>
            <!-- Если пользователь не авторизован, показываем ссылки для регистрации и входа -->
            <a href="register.php">Registratsioon</a>
            <a href="login.php">Logi sisse</a>
        <?php endif; ?>
    </nav>
</header>
