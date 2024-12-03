<?php
global $yhendus;
include('config.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $query = $yhendus->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $query->bind_param("ss", $username, $password);
    if ($query->execute()) {
        echo "Регистрация успешна!";
    } else {
        echo "Ошибка: " . $query->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registratsioon</title>
</head>
<body>
<?php
include("header.php");
?>
<h1>Registreerimine</h1>
<form method="POST" action="register.php">
    <input type="text" name="username" placeholder="Kasutaja nimi" required>
    <input type="password" name="password" placeholder="Parool" required>
    <button type="submit">Registreeri</button>
</form>
</body>
</html>
