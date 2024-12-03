<?php
global $yhendus;
include('config.php');
$query = $yhendus->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tootekataloog</title>
    <link rel="stylesheet" href="styleCatalog.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include("header.php"); ?>
<h1>Kataloog</h1>
<div class="catalog">
    <?php while ($row = $query->fetch_assoc()): ?>
        <div class="product">
            <h2><?php echo $row['name']; ?></h2>
            <p>Hind: <?php echo $row['price']; ?> mündid</p>
            <form method="POST" action="purchase.php">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <button type="submit">Lisa ostukorvi</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

<script>
    // Функция для отображения всплывающего сообщения
    function showAlert(message) {
        const alertBox = document.createElement('div');
        alertBox.classList.add('alert-box');
        alertBox.textContent = message;
        document.body.appendChild(alertBox);

        setTimeout(() => {
            alertBox.remove();
        }, 3000);
    }
</script>
</body>
</html>

