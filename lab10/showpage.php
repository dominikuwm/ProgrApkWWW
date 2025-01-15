<?php
include('cfg.php');  // Dołączenie konfiguracji bazy danych

function PokazPodstrone($id) {
    global $link;

    // Zabezpieczenie przed SQL Injection
    $id_clear = htmlspecialchars($id);

    // Przygotowanie zapytania SQL
    $query = "SELECT page_content FROM page_list_ WHERE alias = ? AND status = 1 LIMIT 1";
    $stmt = $link->prepare($query);
    $stmt->bind_param("s", $id_clear);  // Powiązanie parametru aliasu jako string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Wyświetlenie treści podstrony
        return $row['page_content'];
    } else {
        return "<h3>Nie znaleziono podstrony.</h3>";
    }
}
?>
