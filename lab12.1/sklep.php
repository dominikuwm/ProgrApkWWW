<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep - Koszyk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        .category-filter {
            text-align: center;
            margin-bottom: 20px;
        }
        .category-filter a {
            margin: 5px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .category-filter a:hover {
            background-color: #0056b3;
        }
        .product-card {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .product-card p {
            margin: 5px 0;
        }
        form {
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        td a {
            color: red;
            text-decoration: none;
        }
        td a:hover {
            text-decoration: underline;
        }
        .cart-total {
            font-size: 18px;
            margin-top: 20px;
            text-align: right;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-remove {
            background-color: red;
            padding: 5px 10px;
            margin: 5px;
            font-size: 14px;
        }
        .btn-remove:hover {
            background-color: #b30000;
        }
        .empty-cart {
            text-align: center;
            margin-top: 30px;
            font-size: 20px;
            color: #666;
        }
        .nav-btn {
            display: block;
            margin: 20px auto;
            width: 200px;
            text-align: center;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        .nav-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<?php
session_start(); // Rozpoczęcie sesji
include('cfg.php'); // Plik z konfiguracją bazy danych

// Inicjalizacja koszyka
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Obsługa dodawania do koszyka
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $query = $link->prepare("SELECT * FROM produkty WHERE id = ?");
    $query->bind_param('i', $product_id);
    $query->execute();
    $result = $query->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        $available_stock = (int)$product['ilosc_sztuk'];

        if (isset($_SESSION['cart'][$product_id])) {
            $current_quantity = $_SESSION['cart'][$product_id]['quantity'];
            $new_quantity = $current_quantity + $quantity;

            if ($new_quantity > $available_stock) {
                $_SESSION['cart'][$product_id]['quantity'] = $available_stock;
                echo "<p style='color:red; text-align:center;'>Nie można dodać więcej niż $available_stock sztuk tego produktu!</p>";
            } else {
                $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
            }
        } else {
            if ($quantity > $available_stock) {
                $quantity = $available_stock;
                echo "<p style='color:red; text-align:center;'>Nie można dodać więcej niż $available_stock sztuk tego produktu!</p>";
            }
            $_SESSION['cart'][$product_id] = [
                'title' => $product['tytul'],
                'price_netto' => $product['cena_netto'],
                'vat' => $product['podatek_vat'],
                'quantity' => $quantity,
            ];
        }
    }
}

// Obsługa zmniejszenia ilości produktu o 1
if (isset($_GET['decrease'])) {
    $product_id = (int)$_GET['decrease'];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity']--;
        if ($_SESSION['cart'][$product_id]['quantity'] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

// Obsługa opróżniania koszyka
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
}

// Link powrotu do strony głównej
echo "<a href='index.php' class='nav-btn'>Powrót do strony głównej</a>";

// Wyświetlanie kategorii
echo "<div class='category-filter'>";
echo "<a href='sklep.php'>Wszystkie produkty</a>";
$category_query = $link->query("SELECT * FROM kategorie");
while ($category = $category_query->fetch_assoc()) {
    echo "<a href='sklep.php?kategoria=" . $category['id'] . "'>" . htmlspecialchars($category['nazwa']) . "</a>";
}
echo "</div>";

// Wyświetlanie listy produktów
$category_filter = isset($_GET['kategoria']) ? "WHERE kategoria = " . (int)$_GET['kategoria'] : "";
$query = $link->query("SELECT * FROM produkty $category_filter");
echo "<h1>Produkty</h1>";
echo "<div class='products-list'>";
if ($query->num_rows > 0) {
    while ($row = $query->fetch_assoc()) {
        echo "<div class='product-card'>";
        echo "<p><strong>" . htmlspecialchars($row['tytul']) . "</strong></p>";
        echo "<p>Cena netto: " . number_format($row['cena_netto'], 2) . " zł</p>";
        echo "<p>VAT: " . $row['podatek_vat'] . "%</p>";
        echo "<p>Dostępna ilość: " . $row['ilosc_sztuk'] . " szt.</p>";
        echo "<form method='post'>";
        echo "<label>Ilość: </label>";
        echo "<input type='number' name='quantity' value='1' min='1'>";
        echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
        echo "<button class='btn' type='submit' name='add_to_cart'>Dodaj do koszyka</button>";
        echo "</form>";
        echo "</div>";
    }
} else {
    echo "<p class='empty-cart'>Brak produktów w wybranej kategorii.</p>";
}
echo "</div>";

// Wyświetlanie koszyka
echo "<h2>Twój koszyk</h2>";
if (!empty($_SESSION['cart'])) {
    echo "<table>";
    echo "<tr><th>Nazwa produktu</th><th>Cena netto</th><th>VAT (%)</th><th>Ilość</th><th>Cena brutto</th><th>Usuń 1 szt.</th></tr>";
    $total = 0;
    foreach ($_SESSION['cart'] as $id => $item) {
        $price_brutto = $item['price_netto'] * (1 + $item['vat'] / 100);
        $subtotal = $price_brutto * $item['quantity'];
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['title']) . "</td>";
        echo "<td>" . number_format($item['price_netto'], 2) . " zł</td>";
        echo "<td>" . $item['vat'] . "%</td>";
        echo "<td>" . $item['quantity'] . "</td>";
        echo "<td>" . number_format($subtotal, 2) . " zł</td>";
        echo "<td><a href='sklep.php?decrease=" . $id . "' class='btn-remove'>Usuń 1 szt.</a></td>";
        echo "</tr>";
        $total += $subtotal;
    }
    echo "</table>";
    echo "<p class='cart-total'><strong>Łączna wartość koszyka: " . number_format($total, 2) . " zł</strong></p>";
    echo "<form method='post'><button class='btn' type='submit' name='clear_cart'>Opróżnij koszyk</button></form>";
} else {
    echo "<p class='empty-cart'>Twój koszyk jest pusty.</p>";
}
?>

</body>
</html>
