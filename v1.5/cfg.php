<?php
$servername = "localhost";
$username = "root"; 
$password = "lolek12";     
$dbname = "moja_strona"; 


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>
