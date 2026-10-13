<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$products = [];
$total = 0;
$placeholders = '';
$ids = [];

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare(
        "SELECT id, name, price, stock
         FROM products
         WHERE id IN ($placeholders)
         ORDER BY name"
    );
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

foreach ($products as &$product) {
    $productId = (int)$product['id'];
    $product['quantity'] = (int)($cart[$productId] ?? 0);
    $product['item_sum'] = $product['quantity'] * (float)$product['price'];
    $total += $product['item_sum'];
}
unset($product);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'place_order') {
        $customerName = trim($_POST['customer_name'] ?? '');
        $customerPhone = trim($_POST['customer_phone'] ?? '');

        if ($customerName === '' || $customerPhone === '') {
            exit('Заполните имя и телефон.');
        }

        if (empty($cart)) {
            exit('Ваша корзина пуста.');
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "SELECT id, name, price, stock
                 FROM products
                 WHERE id IN ($placeholders)
                 FOR UPDATE"
            );
            $stmt->execute($ids);
            $dbProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dbById = [];
            foreach ($dbProducts as $product) {
                $dbById[(int)$product['id']] = $product;
            }

            foreach ($cart as $productId => $quantity) {
                $productId = (int)$productId;
                $quantity = (int)$quantity;

                if (
                    !isset($dbById[$productId]) ||
                    $quantity < 1 ||
                    $quantity > (int)$dbById[$productId]['stock']
                ) {
                    $pdo->rollBack();
                    exit('Один из товаров недоступен в указанном количестве.');
                }
            }

            $stmt = $pdo->prepare(
                "INSERT INTO orders (customer_name, customer_phone, total)
                 VALUES (:customer_name, :customer_phone, :total)"
            );
            $stmt->execute([
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'total' => $total
            ]);

            $orderId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, product_name, price, quantity, item_sum)
                 VALUES (:order_id, :product_id, :product_name, :price, :quantity, :item_sum)"
            );

            foreach ($cart as $productId => $quantity) {
                $product = $dbById[(int)$productId];
                $itemSum = (float)$product['price'] * (int)$quantity;

                $itemStmt->execute([
                    'order_id' => $orderId,
                    'product_id' => (int)$productId,
                    'product_name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => (int)$quantity,
                    'item_sum' => $itemSum
                ]);
            }

            $stockStmt = $pdo->prepare(
                "UPDATE products
                 SET stock = stock - :quantity
                 WHERE id = :id AND stock >= :quantity"
            );

            foreach ($cart as $productId => $quantity) {
                $stockStmt->execute([
                    'quantity' => (int)$quantity,
                    'id' => (int)$productId
                ]);

                if ($stockStmt->rowCount() !== 1) {
                    $pdo->rollBack();
                    exit('Не удалось обновить остаток товара.');
                }
            }

            $pdo->commit();
            $_SESSION['cart'] = [];

            header('Location: order_success.php?id=' . $orderId);
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            exit('Ошибка при оформлении заказа: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление заказа</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Оформление заказа</h1>

    <div class="checkout-products">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <article class="checkout-item">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p>Количество: <?= (int)$product['quantity'] ?></p>
                    <p>Сумма: <?= number_format((float)$product['item_sum'], 2, ',', ' ') ?> €</p>
                </article>
            <?php endforeach; ?>
            <p><strong>Итого: <?= number_format($total, 2, ',', ' ') ?> €</strong></p>
        <?php else: ?>
            <p>Ваша корзина пуста. <a href="index.php">Вернуться в каталог</a></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($products)): ?>
        <form method="POST" action="checkout.php">
            <input type="hidden" name="action" value="place_order">

            <p>
                <label for="customerName">Имя:</label><br>
                <input id="customerName" name="customer_name" type="text" required>
            </p>

            <p>
                <label for="customerPhone">Телефон:</label><br>
                <input id="customerPhone" name="customer_phone" type="tel" required>
            </p>

            <button type="submit">Оформить заказ</button>
        </form>
    <?php endif; ?>
</body>
</html>
