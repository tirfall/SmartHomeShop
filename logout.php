<?php
session_start();

// Удаляем все данные сессии
session_unset();

// Уничтожаем сессию
session_destroy();

// Перенаправляем на главную страницу или страницу входа
header("Location: index.php");
exit();
?>