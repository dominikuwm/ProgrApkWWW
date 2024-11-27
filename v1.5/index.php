<?php
    // Wyłączenie raportowania wybranych błędów, aby nie zakłócały działania
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

    // Dołączenie konfiguracji połączenia z bazą danych
    include('cfg.php'); 
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="Content-Language" content="pl">
    <meta name="Author" content="Dominik Gutowski">
    <title>Japońskie samochody</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/timedate.js"></script>
    <script src="js/kolortlo.js"></script>
</head>
<body class="index-page">

<div class="image-container">
    <img src="obrazki/naStroneGlowna.jpg" alt="Japońskie samochody" class="header-image">
</div>

<!-- nawigacja na Stronie -->
<nav>
    <ul>
        <li><a href="index.php?idp=glowna">Strona Główna</a></li>
        <li><a href="index.php?idp=kontakt">Kontakt</a></li>
        <li><a href="index.php?idp=historia">Historia japońskich samochodów</a></li>
        <li><a href="index.php?idp=popmar">Najpopularniejsze marki</a></li>
        <li><a href="index.php?idp=motorsport">Motorsport i tuning</a></li>
        <li><a href="index.php?idp=samelek">Samochody elektryczne i hybrydowe</a></li>
        <li><a href="index.php?idp=lab3">Lab 3</a></li>
        <li><a href="index.php?idp=filmy">Filmy</a></li>
    </ul>
</nav>

<!-- glowna czesc strony -->
<main>
    <?php
        // Pobieranie aliasu podstrony z parametru URL (domyślnie 'glowna')
        $alias = $_GET['idp'] ?? 'glowna';

        // Przygotowanie zapytania SQL do pobrania zawartości podstrony na podstawie aliasu
        $query = "SELECT page_content FROM page_list WHERE alias = ? AND status = 1 LIMIT 1";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            // Przypisanie parametru do zapytania SQL
            $stmt->bind_param("s", $alias);
            $stmt->execute();
            $result = $stmt->get_result();

            // Sprawdzenie, czy wynik istnieje, a jeśli tak - wyświetlenie treści
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo $row['page_content'];
            } else {
                // Wyświetlenie komunikatu, jeśli strona nie istnieje
                echo '<p>Strona nie została znaleziona. Prosimy wybrać inną opcję z menu.</p>';
            }

            // Zamykanie zapytania
            $stmt->close();
        } else {
            echo '<p>Błąd przygotowania zapytania do bazy danych.</p>';
        }

        // Zamykanie połączenia z bazą danych
        $conn->close();
    ?>
</main>


<footer>
    <?php
        $nr_indeksu = '169243';
        $nrGrupy = '2';
        echo 'Autor: Dominik Gutowski, Nr Indeksu: '.$nr_indeksu.', Grupa: '.$nrGrupy;
    ?>
</footer>

</body>
</html>
