<?php
require_once 'db.php';
session_start();
$search = $_GET["search"] ?? "";
$category = (int)($_GET['category'] ?? 0); 
$sort = $_GET['sort'] ?? 'name'; 

$categoryStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT
        products.id,
        products.name,
        products.category_id,
        products.price,
        products.description,
        products.stock,
        products.rating,
        categories.name AS category
    FROM products
    JOIN categories
        ON products.category_id = categories.id";

$params = []; 
$sql .= " WHERE 1=1";

if ($category > 0) {
    $sql .= " AND products.category_id = :category";
    $params['category'] = $category;
}
if ($search !== '') {
    $sql .= " AND (products.name LIKE :search OR products.description LIKE :search_desc)";
    $params['search'] = '%' . $search . '%';
    $params['search_desc'] = '%' . $search . '%';
}

switch ($sort) {
    case 'price_asc':
        $orderBy = 'products.price ASC';
        break;
    case 'price_desc':
        $orderBy = 'products.price DESC';
        break;
    case 'rating_desc':
        $orderBy = 'products.rating DESC';
        break;
    case 'name':
    default:
        $orderBy = 'products.name ASC';
        break;
}
$sql .= " ORDER BY $orderBy";

$perPage = 6;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$sql .= " LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $val) {
    if (is_int($val)) {
        $stmt->bindValue(':' . $key, $val, PDO::PARAM_INT);
    } else {
        $stmt->bindValue(':' . $key, $val, PDO::PARAM_STR);
    }
}

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$countSql = "SELECT COUNT(*) FROM products WHERE 1=1";
$countParams = [];

if ($category > 0) {
    $countSql .= " AND products.category_id = :category";
    $countParams['category'] = $category;
}
if ($search !== '') {
    $countSql .= " AND (products.name LIKE :search OR products.description LIKE :search_desc)";
    $countParams['search'] = '%' . $search . '%';
    $countParams['search_desc'] = '%' . $search . '%';
}

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($countParams);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages = (int)ceil($totalProducts / $perPage);
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title>Document</title>
</head>
<body>
    <main>
        <div class="beanie">
            <h1>Каталог туристического снаряжения</h1>
        </div>

        <form method="GET" action="index.php" class="beanie-foot">
            <label for="categoryFilter">Категория:</label>
            <select name="category" id="categoryFilter">
                <option value="0" <?= $category === 0 ? 'selected' : '' ?>>Все категории</option>
                <?php foreach ($categories as $itemCategory): ?>
                    <option
                        value="<?= (int)$itemCategory['id'] ?>"
                        <?= (int)$category === (int)$itemCategory['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($itemCategory['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="searchInput">Поиск:</label>
            <input
                id="searchInput"
                name="search"
                type="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Введите название товара"
            >
            
            <select name="sort">
                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>По названию</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Цена: по возрастанию</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Цена: по убыванию</option>
                <option value="rating_desc" <?= $sort === 'rating_desc' ? 'selected' : '' ?>>По рейтингу</option>
            </select>
            
            <button type="submit">Применить</button>
        </form>

        <section class="products">
            <?php foreach ($products as $product): ?>
                <article class="product-card" data-category="<?= htmlspecialchars($product["category"]) ?>">
                    <h2><a href="product.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product["name"]) ?></a></h2>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p>Категория: <?= htmlspecialchars($product["category"]) ?></p>
                    <p>Цена: <?= number_format($product['price'], 2, ',', ' ') ?> €</p>
                    <p>Рейтинг: <?= htmlspecialchars($product["rating"]) ?></p>
                    <p>
                        <?php if ($product['stock'] > 0): ?>
                            В наличии: <?= (int)$product['stock'] ?>
                        <?php else: ?>
                            Нет в наличии
                        <?php endif; ?>
                    </p>
                </article>
            <?php endforeach; ?>
        </section>

        <div class="pagination" style="margin: 20px 0;">
            <?php for ($i = 1; $i <= $totalPages; $i++): 
                $queryParams = [
                    'search' => $search,
                    'category' => $category,
                    'sort' => $sort,
                    'page' => $i
                ];
                $url = 'index.php?' . http_build_query($queryParams);
            ?>
                <a href="<?= $url ?>" style="margin-right: 5px; padding: 5px; border: 1px solid #ccc; <?= $page === $i ? 'font-weight: bold; background: #eee;' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </main>  

     <script>
    const products = <?= json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script src="js/app.js"></script>
</body>
</html>
