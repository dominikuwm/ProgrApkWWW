<?php
// Dołączenie konfiguracji bazy danych oraz pliku z funkcją PokazPodstrone
include('showpage.php');
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
    <script src="js/timedate.js" type="text/javascript"></script>
    <script src="js/kolortlo.js" type="text/javascript"></script>
</head>
<body onload="startclock()">
    <!-- Nawigacja -->
    <nav>
        <ul>
            <li><a href="index.php?id=glowna">Strona Główna</a></li>
            <li><a href="index.php?id=kontakt">Kontakt</a></li>
            <li><a href="index.php?id=historia">Historia japońskich samochodów</a></li>
            <li><a href="index.php?id=popmar">Najpopularniejsze marki</a></li>
            <li><a href="index.php?id=motorsport">Motorsport i tuning</a></li>
            <li><a href="index.php?id=samelek">Samochody elektryczne i hybrydowe</a></li>
            <li><a href="index.php?id=lab3">Lab 3</a></li>
            <li><a href="index.php?id=filmy">Filmy</a></li>
        </ul>
    </nav>

    <!-- Główna treść -->
    <div class="content">
        <div style="text-align: center; margin: 20px 0; font-size: 20px;">
            <span id="zegarek">Ładowanie czasu...</span>
        </div>
        <?php
        // Pobranie ID strony z URL
        $id = $_GET['id'] ?? 'glowna';  // Domyślna strona to "glowna"

        // Wywołanie funkcji do wyświetlenia treści podstrony
        echo PokazPodstrone($id);
        ?>
    </div>

    <!-- Stopka -->
    <footer style="padding: 20px 0; background-color: #f8f9fa; text-align: center;">
        <?php
        $nr_indeksu = '169243';
        $nrGrupy = '2';
        echo 'Autor: Dominik Gutowski, nr indeksu: ' . $nr_indeksu . ', grupa: ISI ' . $nrGrupy . '';
        ?>
        <!-- Panel zmiany kolorów -->
        <div class="color-buttons" style="display: flex; justify-content: center; gap: 10px;">
            <button onclick="changeBackground('#f5f5f5')">Domyślny</button>
            <button onclick="changeBackground('#FF6347')">Czerwony</button>
            <button onclick="changeBackground('#90EE90')">Zielony</button>
            <button onclick="changeBackground('#87CEEB')">Niebieski</button>
        </div>
    </footer>

    <script>
        function showtime() {
            let now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();
            let day = now.getDate();
            let month = now.getMonth() + 1;  // Miesiące zaczynają się od 0
            let year = now.getFullYear();

            let timeValue = ((hours > 12) ? hours - 12 : hours) || 12;
            timeValue += ((minutes < 10) ? ":0" : ":") + minutes;
            timeValue += ((seconds < 10) ? ":0" : ":") + seconds;
            timeValue += (hours >= 12) ? " PM" : " AM";

            let dateValue = `${day}/${month}/${year}`;
            let zegarekElement = document.getElementById("zegarek");

            if (zegarekElement) {
                zegarekElement.innerHTML = `${dateValue} ${timeValue}`;
                setTimeout(showtime, 1000);
            }
        }

        document.addEventListener('DOMContentLoaded', showtime);
    </script>
</body>
</html>


