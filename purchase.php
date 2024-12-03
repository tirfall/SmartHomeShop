<?php
global $yhendus;
include('config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Palun logi sisse.");
}

$product_id = $_POST['product_id'];
$user_id = $_SESSION['user_id'];

// Проверка наличия товара
$product_query = $yhendus->prepare("SELECT id FROM products WHERE id = ?");
$product_query->bind_param("i", $product_id);
$product_query->execute();
$product_result = $product_query->get_result();

if ($product_result->num_rows === 0) {
    die("Toodet ei leitud.");
}

// Добавление товара в корзину
$add_to_cart = $yhendus->prepare("
    INSERT INTO cart (user_id, product_id, quantity) 
    VALUES (?, ?, 1) 
    ON DUPLICATE KEY UPDATE quantity = quantity + 1
");
$add_to_cart->bind_param("ii", $user_id, $product_id);
$add_to_cart->execute();

// Ответ для клиента
echo "<script>
    alert('Toode edukalt lisatud ostukorvi!');
    window.location.href = 'catalog.php';
</script>";
?>
