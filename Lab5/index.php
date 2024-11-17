<?php
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
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
        <li><a href="index.php?idp=filmy">filmy</a></li>
    </ul>
</nav>

<!-- glowna czesc strony -->
<main>
    <?php
        $pages = [
            'glowna' => 'html/glowna.html',
            'kontakt' => 'html/kontakt.html',
            'historia' => 'html/historia.html',
            'popmar' => 'html/popmar.html',
            'motorsport' => 'html/motorsport.html',
            'samelek' => 'html/samelek.html',
            'lab3' => 'html/lab3.html',
            'filmy' => 'html/filmy.html'
        ];

        $page = $_GET['idp'] ?? 'glowna';
        $strona = $pages[$page] ?? 'html/glowna.html';

        if (file_exists($strona)) {
            include($strona);
        } else {
            echo 'Page not found.';
        }
    ?>
</main>

<!-- Footer Information -->
<footer>
    <?php
        $nr_indeksu = '169243';
        $nrGrupy = '2';
        echo 'Autor: Dominik Gutowski, Nr Indeksu: '.$nr_indeksu.', Grupa: '.$nrGrupy;
    ?>
</footer>

</body>
</html>
