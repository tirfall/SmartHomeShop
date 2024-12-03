<?php
session_start();
global $yhendus;
include('config.php');

// Предполагаем, что пользователь уже авторизован
// Получение информации о пользователе
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php"); // Перенаправление на страницу входа
    exit();
}

// Получение информации о балансе и корзине пользователя
$user_query = $yhendus->prepare("SELECT username, email, balance FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_info = $user_query->get_result()->fetch_assoc();

// Получение содержимого корзины
$cart_query = $yhendus->prepare("
    SELECT p.name, p.price, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?;
");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_items = $cart_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isiklik konto</title>
    <link rel="stylesheet" href="styleKasutaja.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
include("header.php");
?>

<h1>Isiklik konto</h1>
<div class="profile-container">
    <div class="account-info">
        <h2>Konto teave</h2>
        <p><strong>Kasutaja nimi:</strong> <?php echo htmlspecialchars($user_info['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?></p>
        <p><strong>Tasakaal:</strong> <?php echo htmlspecialchars($user_info['balance']); ?> mündid</p>
    </div>

    <div class="cart">
        <h2>Ostukorv</h2>
        <?php if ($cart_items->num_rows > 0): ?>
            <ul>
                <?php while ($item = $cart_items->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong> —
                        <?php echo htmlspecialchars($item['quantity']); ?> tk.,
                        <?php echo htmlspecialchars($item['price'] * $item['quantity']); ?> mündid
                    </li>
                <?php endwhile; ?>
            </ul>
            <form action="checkout.php" method="POST">
                <button type="submit">Esitage tellimus</button>
            </form>
        <?php else: ?>
            <p>Ostukorv tühi</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
