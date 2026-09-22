<?php
require_once 'db.php';

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title><?= htmlspecialchars($product['name']) ?></title>
</head>
<body>
    <main style="padding: 20px;">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p>Категория: <?= htmlspecialchars($product['category']) ?></p>
        <p><?= htmlspecialchars($product['description']) ?></p>
        <p>Цена: <?= number_format($product['price'], 2, ',', ' ') ?> €</p>
        <p>Рейтинг: <?= htmlspecialchars($product['rating']) ?></p>
        
        <?php if ($product['stock'] > 0): ?>
            <p>В наличии: <?= (int)$product['stock'] ?></p>
        <?php else: ?>
            <p>Нет в наличии</p>
        <?php endif; ?>
        
        <br>
        <a href="index.php">← Вернуться в каталог</a>
    </main>
</body>
</html>
