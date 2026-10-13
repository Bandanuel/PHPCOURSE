<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        $stmt = $pdo->prepare("SELECT id, stock FROM products WHERE id = :id");
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $currentQuantity = $_SESSION['cart'][$productId] ?? 0;
            $newQuantity = $currentQuantity + $quantity;

            if ($newQuantity > (int)$product['stock']) {
                $newQuantity = (int)$product['stock'];
            }
            if ($newQuantity > 0) {
                $_SESSION['cart'][$productId] = $newQuantity;
            }
        }
    }

    if ($action === 'update') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
            $stmt->execute(['id' => $productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $quantity = min($quantity, (int)$product['stock']);
                if ($quantity > 0) {
                    $_SESSION['cart'][$productId] = $quantity;
                } else {
                    unset($_SESSION['cart'][$productId]);
                }
            }
        }
    }

    if ($action === 'remove') {
        $productId = (int)($_POST['product_id'] ?? 0);
        unset($_SESSION['cart'][$productId]);
    }

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
    }

    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'];
$products = [];
$total = 0;

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

    foreach ($products as &$product) {
        $productId = (int)$product['id'];
        $product['quantity'] = (int)($cart[$productId] ?? 0);
        $product['sum'] = $product['quantity'] * (float)$product['price'];
        $total += $product['sum'];
    }
    unset($product);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина — Adventure Shop</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <main class="container">
        <h1>Ваша корзина</h1>

        <?php if (empty($products)): ?>
            <div class="cart-empty">
                <p>Корзина пуста.</p>
                <a href="index.php">← Вернуться в каталог</a>
            </div>
        <?php else: ?>
            <div class="cart-actions">
                <form method="POST" action="cart.php">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn btn-clear">Очистить корзину</button>
                </form>
            </div>

            <div class="cart">
                <?php foreach ($products as $product): ?>
                    <article class="cart-item">
                        <h2><?= htmlspecialchars($product['name']) ?></h2>
                        <p>Цена: <?= number_format($product['price'], 2, ',', ' ') ?> €</p>
                        <p>Доступно на складе: <?= (int)$product['stock'] ?> шт.</p>
                        <p>Сумма: <strong><?= number_format($product['sum'], 2, ',', ' ') ?> €</strong></p>

                        <div class="item-controls">
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                <input 
                                    type="number" 
                                    name="quantity" 
                                    value="<?= (int)$product['quantity'] ?>" 
                                    min="0" 
                                    max="<?= (int)$product['stock'] ?>"
                                >
                                <button type="submit">Обновить</button>
                            </form>

                            <form method="POST" action="cart.php">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                <button type="submit" class="btn-remove">Удалить</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="cart-footer">
                <h3>Итого к оплате: <?= number_format($total, 2, ',', ' ') ?> €</h3>
                <div class="footer-links">
                    <a href="index.php">← Продолжить покупки</a>
                    <a href="checkout.php" class="btn-checkout">Оформить заказ →</a>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
