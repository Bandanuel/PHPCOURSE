<?php
require_once 'db.php';

$orderId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT id, customer_name, total, created_at
     FROM orders
     WHERE id = :id"
);
$stmt->execute(['id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    http_response_code(404);
    exit('Заказ не найден.');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заказ успешно оформлен</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Заказ оформлен</h1>
    <p>Номер заказа: <strong>#<?= (int)$order['id'] ?></strong></p>
    <p>Покупатель: <?= htmlspecialchars($order['customer_name']) ?></p>
    <p>Сумма: <?= number_format((float)$order['total'], 2, ',', ' ') ?> €</p>
    <p>Дата оформления: <?= htmlspecialchars($order['created_at']) ?></p>

    <nav style="margin-top: 20px;">
        <a href="index.php">Каталог</a> | 
        <a href="cart.php">Корзина</a>
    </nav>
</body>
</html>
