<?php
require_once 'db.php';
session_start();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT
        products.id,
        products.name,
        products.price,
        products.description,
        products.stock,
        products.rating,
        categories.name AS category
     FROM products
     JOIN categories
        ON products.category_id = categories.id
     WHERE products.id = :id"
);
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Товар не найден.');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title><?= htmlspecialchars($product['name']) ?></title>
</head>
<body>
    <main style="padding: 20px;">
        <nav style="margin: 10px 0; text-align: right;">
            <a href="cart.php" style="font-weight: bold; font-size: 1.1em;">🛒 Корзина</a>
        </nav>

        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p>Категория: <?= htmlspecialchars($product['category']) ?></p>
        <p><?= htmlspecialchars($product['description']) ?></p>
        <p>Цена: <?= number_format($product['price'], 2, ',', ' ') ?> €</p>
        <p>Рейтинг: <?= htmlspecialchars($product['rating']) ?></p>
        
        <?php if ($product['stock'] > 0): ?>
            <p>В наличии: <?= (int)$product['stock'] ?></p>
            
            <form method="POST" action="cart.php" style="margin-top: 20px; padding: 15px; border: 1px solid #ddd; display: inline-block;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <label for="quantity">Количество:</label>
                <input
                    id="quantity"
                    type="number"
                    name="quantity"
                    value="1"
                    min="1"
                    max="<?= (int)$product['stock'] ?>"
                    style="width: 60px; padding: 5px;"
                >
                <button type="submit" style="padding: 5px 15px; background: #28a745; color: #fff; border: none; cursor: pointer;">Добавить в корзину</button>
            </form>
        <?php else: ?>
            <p style="color: red; font-weight: bold;">Нет в наличии</p>
        <?php endif; ?>
        
        <br><br>
        <a href="index.php">← Вернуться в каталог</a>
    </main>
</body>
</html>
