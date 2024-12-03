<?php
session_start();
global $yhendus;
include('config.php');

// Предполагаем, что пользователь авторизован
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php"); // Перенаправление на страницу входа, если пользователь не авторизован
    exit();
}

// Получение информации о балансе пользователя
$user_query = $yhendus->prepare("SELECT balance FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_info = $user_query->get_result()->fetch_assoc();

// Получение содержимого корзины
$cart_query = $yhendus->prepare("
    SELECT p.name, p.price, c.quantity, p.id 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_items = $cart_query->get_result();

// Вычисление общей стоимости
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Проверка наличия достаточного баланса
if ($user_info['balance'] >= $total_price) {
    // Списание средств с баланса
    $new_balance = $user_info['balance'] - $total_price;
    $update_balance = $yhendus->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $update_balance->bind_param("di", $new_balance, $user_id);
    $update_balance->execute();

    // Оформление заказа (например, создание записи о заказе в базе данных)
    // Здесь можно добавить код для сохранения заказа, например:
    $order_query = $yhendus->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, ?)");
    $order_status = 'pending';  // Статус заказа, например "ожидает обработки"
    $order_query->bind_param("dis", $user_id, $total_price, $order_status);
    $order_query->execute();

    $order_id = $yhendus->insert_id; // Получение ID созданного заказа

    // Перенос товаров из корзины в заказ
    $cart_query = $yhendus->prepare("
    SELECT c.product_id, c.quantity, p.price 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $cart_items = $cart_query->get_result();


    while ($item = $cart_items->fetch_assoc()) {
        $order_items_query = $yhendus->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price) 
        VALUES (?, ?, ?, ?)
    ");
        $order_items_query->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $order_items_query->execute();
    }

    // Очистка корзины пользователя после оформления заказа
    $clear_cart = $yhendus->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear_cart->bind_param("i", $user_id);
    $clear_cart->execute();

    // Перенаправление на страницу успешного оформления заказа
    header("Location: order_success.php?order_id=$order_id");
    // После успешного создания заказа
    $_SESSION['last_order_id'] = $order_id;
    header("Location: order_success.php");
    exit();
} else {
    // Если на балансе недостаточно средств
    echo "<p>Teil ei ole tellimuse esitamiseks piisavalt raha.</p>";
    echo "<p><a href='kasutaja.php'>Naaske oma isiklikule kontole</a></p>";
}



?>
