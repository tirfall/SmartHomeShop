<?php
global $yhendus;
session_start();
include('config.php'); // Подключение к базе данных

// Проверка прав администратора
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php"); // Перенаправляем на главную страницу, если не администратор
    exit();
}

// Изменение баланса пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_balance'])) {
    $user_id = $_POST['user_id'];
    $new_balance = $_POST['new_balance'];

    $update_balance_query = $yhendus->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $update_balance_query->bind_param("di", $new_balance, $user_id);
    $update_balance_query->execute();
}

// Добавление нового продукта
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];

    $add_product_query = $yhendus->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
    $add_product_query->bind_param("sd", $product_name, $product_price);
    $add_product_query->execute();
}

// Удаление продукта
if (isset($_GET['delete_product'])) {
    $product_id = $_GET['delete_product'];

    $delete_product_query = $yhendus->prepare("DELETE FROM products WHERE id = ?");
    $delete_product_query->bind_param("i", $product_id);
    $delete_product_query->execute();
}

// Получаем список пользователей
$users_query = $yhendus->query("SELECT id, username, balance FROM users");

// Получаем список продуктов
$products_query = $yhendus->query("SELECT id, name, price FROM products");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleAdmin.css">
</head>
<body>
<?php include("header.php"); ?>
<h1>Admin Panel</h1>

<h2>Manage User Balances</h2>
<form method="POST" action="">
    <label for="user_id">User ID:</label>
    <select name="user_id">
        <?php while ($user = $users_query->fetch_assoc()): ?>
            <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?> (Balance: <?php echo $user['balance']; ?>)</option>
        <?php endwhile; ?>
    </select>
    <br>
    <label for="new_balance">New Balance:</label>
    <input type="number" step="0.01" name="new_balance" required>
    <button type="submit" name="update_balance">Update Balance</button>
</form>

<h2>Add New Product</h2>
<form method="POST" action="">
    <label for="product_name">Product Name:</label>
    <input type="text" name="product_name" required>
    <br>
    <label for="product_price">Price:</label>
    <input type="number" step="0.01" name="product_price" required>
    <button type="submit" name="add_product">Add Product</button>
</form>

<h2>Manage Products</h2>
<table border="1">
    <tr>
        <th>Product Name</th>
        <th>Price</th>
        <th>Actions</th>
    </tr>
    <?php while ($product = $products_query->fetch_assoc()): ?>
        <tr>
            <td><?php echo $product['name']; ?></td>
            <td><?php echo $product['price']; ?> монет</td>
            <td>
                <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a>
                <a href="?delete_product=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
