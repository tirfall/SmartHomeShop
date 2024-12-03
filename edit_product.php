<?php
global $yhendus;
session_start();
include('config.php'); // Подключение к базе данных

// Проверка прав администратора
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php"); // Перенаправляем на главную страницу, если не администратор
    exit();
}

// Проверка наличия параметра id в URLss
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin.php"); // Перенаправляем в админ-панель, если нет id
    exit();
}

$product_id = $_GET['id'];

// Получаем данные о продукте из базы данных
$product_query = $yhendus->prepare("SELECT id, name, price FROM products WHERE id = ?");
$product_query->bind_param("i", $product_id);
$product_query->execute();
$product_result = $product_query->get_result();

// Если продукт не найден
if ($product_result->num_rows == 0) {
    echo "Toote ei leidnud!";
    exit();
}

$product = $product_result->fetch_assoc();

// Обработка формы редактирования продукта
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $new_name = $_POST['product_name'];
    $new_price = $_POST['product_price'];

    // Обновляем данные о продукте в базе данных
    $update_product_query = $yhendus->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
    $update_product_query->bind_param("sdi", $new_name, $new_price, $product_id);
    $update_product_query->execute();

    // Перенаправляем в админ-панель после обновления
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Toote redigeerimine</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<?php
include("header.php");
?>
<h1>Redigeeri toodet</h1>

<form method="POST" action="">
    <label for="product_name">Toote nimi:</label>
    <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
    <br>
    <label for="product_price">Hind:</label>
    <input type="number" step="0.01" name="product_price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
    <br>
    <button type="submit" name="update_product">Uuenda toodet</button>
</form>

<a href="admin.php">Tagasi administraatoripaneelile</a>
</body>
</html>
