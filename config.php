<?php


// Конфигурация для подключения к базе данных
$serverName = 'localhost'; // Сервер (обычно localhost для XAMPP)
$username = 'root';        // Пользователь (по умолчанию root в XAMPP)
$password = '';            // Пароль (обычно пустой в XAMPP)
$database = 'smart_home';   // Имя вашей базы данных

// Подключение к базе данных
$yhendus = new mysqli($serverName, $username, $password, $database);

// Проверяем соединение
if ($yhendus->connect_error) {
    die('Ошибка подключения к базе данных: ' . $yhendus->connect_error);
}

// Устанавливаем кодировку для работы с UTF-8
$yhendus->set_charset('utf8');

?>
