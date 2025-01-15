<?php
$dbhost = 'localhost';  // Host bazy danych (dla XAMPP domyślnie "localhost")
$dbuser = 'root';       // Nazwa użytkownika (dla XAMPP domyślnie "root")
$dbpass = '';           // Hasło (dla XAMPP puste, chyba że zostało zmienione)
$baza = 'moja_strona_'; // Nazwa Twojej bazy danych

// Połączenie z bazą danych
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

// Sprawdzenie połączenia
if (!$link) {
    die("<b>Przerwane połączenie:</b> " . mysqli_connect_error());
}
?>
