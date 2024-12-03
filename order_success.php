<?php
global $yhendus;
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Проверка, что у нас есть ID последнего заказа
if (!isset($_SESSION['last_order_id'])) {
    die("Нет информации о последнем заказе.");
}

$last_order_id = $_SESSION['last_order_id'];

// Получение информации о заказе
$order_query = $yhendus->prepare("
    SELECT o.id, o.order_date, o.total_price, o.status, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$order_query->bind_param("i", $last_order_id);
$order_query->execute();
$order_result = $order_query->get_result();

if ($order_result->num_rows === 0) {
    die("Заказ не найден.");
}

$order = $order_result->fetch_assoc();

// Получение информации о товарах в заказе
$order_items_query = $yhendus->prepare("
    SELECT p.name, oi.quantity, oi.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$order_items_query->bind_param("i", $last_order_id);
$order_items_query->execute();
$order_items_result = $order_items_query->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include('header.php'); ?>

<div class="order-success">
    <h1>Спасибо за ваш заказ!</h1>
    <p>Номер заказа: <strong>#<?php echo $order['id']; ?></strong></p>
    <p>Имя клиента: <strong><?php echo $order['username']; ?></strong></p>
    <p>Дата заказа: <strong><?php echo $order['order_date']; ?></strong></p>
    <p>Сумма заказа: <strong><?php echo $order['total_price']; ?> монет</strong></p>
    <p>Статус заказа: <strong><?php echo $order['status']; ?></strong></p>

    <h2>Товары в заказе:</h2>
    <table>
        <tr>
            <th>Название</th>
            <th>Количество</th>
            <th>Цена</th>
        </tr>
        <?php while ($item = $order_items_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo $item['price']; ?> монет</td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="catalog.php" class="button">Вернуться в каталог</a>
</div>

</body>
</html>
