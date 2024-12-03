<?php
global $yhendus;
include('config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Проверяем, существует ли пользователь
    $query = $yhendus->prepare("SELECT Id, Password, Balance FROM Users WHERE Username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Проверяем пароль
        if (password_verify($password, $user['Password'])) {
            $_SESSION['user_id'] = $user['Id'];
            $_SESSION['username'] = $username;
            $_SESSION['balance'] = $user['Balance'];

            echo "<script>
                alert('Вход выполнен успешно!');
                window.location.href = 'catalog.php';
            </script>";
            exit;
        } else {
            echo "Неправильный пароль!";
        }
    } else {
        echo "Пользователь не найден!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logi sisse</title>
</head>
<body>
<?php
include("header.php");
?>
<h1>Вход</h1>
<form method="POST" action="login.php">
    <input type="text" name="username" placeholder="Kasutaja nimi" required>
    <input type="password" name="password" placeholder="Parool" required>
    <button type="submit">Logi sisse</button>
</form>
</body>
</html>
