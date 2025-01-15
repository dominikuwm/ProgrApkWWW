<?php
// Plik: cfg.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Rozpoczęcie sesji tylko, jeśli nie jest aktywna
}

$dbhost = 'localhost';  // Host bazy danych
$dbuser = 'root';       // Nazwa użytkownika
$dbpass = '';           // Hasło użytkownika
$baza = 'moja_strona_'; // Nazwa bazy danych

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);
if (!$link) {
    die("<b>Przerwane połączenie:</b> " . mysqli_connect_error());
}

$login = 'root';  // Login administratora
$pass = 'haslo';  // Hasło administratora
?>
