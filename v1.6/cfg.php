<?php
$servername = "localhost";
$username = "root"; 
$password = "lolek12";     
$dbname = "moja_strona"; 

// Dane do logowania do panelu admina
$cfg_login = "root"; 
$cfg_pass = "lolek12"; 

// Tworzenie połączenia z użyciem MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzanie, czy połączenie zostało nawiązane poprawnie
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>
