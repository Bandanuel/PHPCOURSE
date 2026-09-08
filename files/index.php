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

    $number = array();
    $number = [];
    ?>

    <form method="GET">
        <input type="text" name="search">
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