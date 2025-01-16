<?php
// Plik: logout.php
session_start();  // Rozpoczęcie sesji
session_unset();  // Usunięcie wszystkich zmiennych sesji
session_destroy();  // Zniszczenie sesji
header('Location: ../index.php');  // Przekierowanie na stronę główną
exit;
?>