<?php
include('cfg.php');
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Sklep - Lista produktów</title>
    <style>
        .produkt {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            width: 250px;
        }
        .produkt img {
            max-width: 100%;
        }
        .produkt h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <h1>Sklep - Lista produktów</h1>

    <?php
    $query = "SELECT * FROM produkty WHERE status_dostepnosci = 1";
    $result = $link->query($query);

    while ($produkt = $result->fetch_assoc()) {
        echo "<div class='produkt'>";
        echo "<h3>" . htmlspecialchars($produkt['tytul']) . "</h3>";
        echo "<p>" . htmlspecialchars($produkt['opis']) . "</p>";
        echo "<p><strong>Cena netto: " . number_format($produkt['cena_netto'], 2) . " zł</strong></p>";
        echo "<p>VAT: " . htmlspecialchars($produkt['podatek_vat']) . "%</p>";
        echo "<p><strong>Całkowita cena: " . number_format($produkt['cena_netto'] * (1 + $produkt['podatek_vat'] / 100), 2) . " zł</strong></p>";
        echo "<p>Dostępność: " . htmlspecialchars($produkt['ilosc_sztuk']) . " sztuk</p>";
        if (!empty($produkt['zdjecie'])) {
            echo "<img src='" . htmlspecialchars($produkt['zdjecie']) . "' alt='Zdjęcie produktu'>";
        }
        echo "</div>";
    }
    ?>

</body>
</html>
