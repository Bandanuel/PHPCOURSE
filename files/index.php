<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <?php
    session_start();
    $search = $_GET["search"] ?? "";
    echo session_id();
    echo session_name();
    // ПЕРВАЯ ПРОГА НА ПХП
    echo "Шалом, ПХП!";

    //Однострочный комментарий
    #Ещё один однострочный комментарий
    /*Многострочный 
    комментарий */

    $name = "Палатка";
    $price = 12000;
    $weight = 2.5;
    $InStock = true;

    echo $name;
    echo "Цена: " . $price . " руб.";
    
    if ($price > 10000) {
        echo "Высокая цена";
    }

    if ($InStock) {
        echo "В наличии";
    } else {
        echo "Нет в наличии";
    }

    $product = [
        "name" => "Палатка",
        "price" => 12000,
        "category" => "Палатки",
        "InStock" => true
    ];
    echo $product["category"];

    $products = [
        ["name"=>"Палатка","price"=>12000],
        ["name"=>"Рюкзак","price"=>6500]
    ];
    foreach ($products as $product) {
        echo $product["name"];
    };

    if (isset($_GET["search"])) {
        echo htmlspecialchars($_GET["search"]);
    };

    function formatPrice($price) {
        return number_format($price, 0, "", " ") . " руб.";
    };

    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }

    $number = array();
    $number = [];
    
    // Академ час 1
    $shopName = "Adventure Shop"; 
    $city = "Стокгольм"; 
    $year = 2026; 
 
    echo "<h1>$shopName</h1>"; 
    echo "<p>Город: $city</p>"; 
    echo "<p>Год: $year</p>";

    $ProductValue = 30;
    $ProductAvgPrice = 500;
    
    // Академ час 2

    $productName = "Рюкзак"; 
    $price = 6500; 
    $inStock = true; 
    
    if ($price > 5000 && $inStock) { 
        echo "Товар доступен и относится к средней ценовой категории"; 
    } else  {    
        echo "Проверьте наличие товара"; 
    } 
    
    if ($price > 10000) {
        echo "Премиальный товар";
        $price = $price * 0.10;
    };

    if (!$inStock){
        echo "Tовара нет в наличии";
    }

    // Академ час 3
    $products = [ 
    ["id"=>1,"name"=>"Палатка","price"=>12000,"category"=>"Палатки","inStock"=>true, "weight" => 15, "season" => "summer", "Company" => "Abobos"], 
    ["id"=>2,"name"=>"Рюкзак","price"=>6500,"category"=>"Рюкзаки","inStock"=>true, "weight" => 0.5, "season" => "summer", "Company" => "Nige"], 
    ["id"=>3,"name"=>"Спальный мешок","price"=>8000,"category"=>"Снаряжение","inStock"=>false, "weight" => 3, "season" => "summer", "Company" => "Dexter"], 
    ["id"=>4,"name"=>"Мангал","price"=>1000,"category"=>"Снаряжение","inStock"=>true, "weight" => 1, "season" => "summer", "Company" => "Nige"], 
    ["id"=>5,"name"=>"Шатёр","price"=>30000,"category"=>"Палатки","inStock"=>true, "weight" => 30, "season" => "summer", "Company" => "Abobos"], 
    ["id"=>6,"name"=>"Мешок","price"=>8000,"category"=>"Рюкзаки","inStock"=>false, "weight" => 0.6, "season" => "summer", "Company" => "Dexter"] 
    ]; 
    
    echo "<p><b>Поиск по категории</b></p>";
    foreach ($products as $product) {
        if ($search == $product["category"]) {
        echo "<h2>" . $product["name"] . "</h2>"; 
        echo "<p>Цена: " . $product["price"] . " руб. </p>"; 
        echo "<p>Категория: " . $product["category"] . "</p>";
        if ($product["inStock"]) { 
            echo "<p>В наличии</p>"; 
        } else { 
            echo "<p>Нет в наличии</p>"; 
        } 
    }
    } 

    //Академ час 4
    echo "<b>Поиск по названию</b>";
    foreach ($products as $product) {
        if ($search == $product["name"]) {
        echo "<h2>" . $product["name"] . "</h2>"; 
        echo "<p>Цена: " . $product["price"] . " руб. </p>"; 
        echo "<p>Категория: " . $product["category"] . "</p>";
        if ($product["inStock"]) { 
            echo "<p>В наличии</p>"; 
        } else { 
            echo "<p>Нет в наличии</p>"; 
        } 
    }
    } 
    foreach ($products as $product) {
        for($prices = []; $product["price"] != -1; $prices  )
        echo "<h2>" . $product["name"] . "</h2>"; 
        echo "<p>Цена: " . $product["price"] . " руб. </p>"; 
        echo "<p>Категория: " . $product["category"] . "</p>";
        if ($product["inStock"]) { 
            echo "<p>В наличии</p>"; 
        } else { 
            echo "<p>Нет в наличии</p>"; 
        } 
    } 

    ?> 
    <form method="GET"> 
    <input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Поиск"> 
    <button type="submit">Найти</button> 
    </form> 


    <header>
        <h1>ROBLOX</h1>
    </header>
    <main>
        <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Expedita nulla nostrum veritatis temporibus,<br> modi eveniet eius itaque aut, voluptates ad incidunt eligendi veniam officiis rem quo, amet quasi ab vel?</p>
    </main>
    <aside>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quas eos corrupti, vero autem, officia natus id ratione obcaecati vitae amet corporis maiores eum sunt hic aut, earum suscipit quibusdam. Praesentium.</p>
    </aside>
    <footer>
        <p>О нас</p>
    </footer>
</body>
</html>