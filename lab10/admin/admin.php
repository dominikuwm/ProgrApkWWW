<?php
session_start();  // Rozpoczęcie sesji
include('../cfg.php');  // Dołączenie konfiguracji z bazą danych

// Obsługa formularza logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['dodajKategorie'])) {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($login === 'root' && $password === 'haslo') {
        $_SESSION['zalogowany'] = true;
        header('Location: admin.php');  // Przekierowanie po zalogowaniu
        exit;
    } else {
        echo "<p style='color: red;'>Błędny login lub hasło!</p>";
    }
}

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
    echo "
    <h2>Zaloguj się</h2>
    <form method='post'>
        <label>Login:</label>
        <input type='text' name='login' required><br>
        <label>Hasło:</label>
        <input type='password' name='password' required><br>
        <button type='submit'>Zaloguj się</button>
    </form>";
    exit;
}

// Panel administracyjny
echo '<h1>Witaj w panelu administratora!</h1>';

// -----------------------------------------
// Obsługa kategorii
// -----------------------------------------

// Funkcja wyświetlania kategorii w formie drzewa
function pokazKategorie($link, $matka = 0, $poziom = 0) {
    $sql = "SELECT * FROM kategorie WHERE matka = ? ORDER BY nazwa";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('i', $matka);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . str_repeat("--", $poziom) . htmlspecialchars($row['nazwa']) . " (ID: " . $row['id'] . ")";
            echo " <a href='?edit_kategoria=" . $row['id'] . "'>Edytuj</a> | ";
            echo "<a href='?delete_kategoria=" . $row['id'] . "' onclick='return confirm(\"Czy na pewno chcesz usunąć tę kategorię i jej podkategorie?\")'>Usuń</a>";
            pokazKategorie($link, $row['id'], $poziom + 1);
            echo "</li>";
        }
        echo "</ul>";
    }
}

// Dodawanie kategorii
if (isset($_POST['dodajKategorie'])) {
    $nazwa = $_POST['nazwa'];
    $matka = $_POST['matka'];
    $sql = "INSERT INTO kategorie (matka, nazwa) VALUES (?, ?)";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('is', $matka, $nazwa);
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Kategoria została dodana!</p>";
    } else {
        echo "<p style='color:red;'>Błąd: " . $link->error . "</p>";
    }
}

// Edycja kategorii
if (isset($_GET['edit_kategoria'])) {
    $id = intval($_GET['edit_kategoria']);
    $query = "SELECT * FROM kategorie WHERE id = ? LIMIT 1";
    $stmt = $link->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo '
    <h2>Edytuj kategorię</h2>
    <form method="post">
        <input type="hidden" name="edit_id" value="' . $id . '">
        <label>Nazwa:</label>
        <input type="text" name="nazwa" value="' . htmlspecialchars($row['nazwa']) . '" required><br>
        <button type="submit" name="update_kategoria">Zapisz zmiany</button>
    </form>';
}

if (isset($_POST['update_kategoria'])) {
    $id = intval($_POST['edit_id']);
    $nazwa = $_POST['nazwa'];
    $sql = "UPDATE kategorie SET nazwa = ? WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('si', $nazwa, $id);
    if ($stmt->execute()) {
        header('Location: admin.php');
        exit;
    } else {
        echo "<p style='color:red;'>Błąd: " . $link->error . "</p>";
    }
}

// Usuwanie kategorii
if (isset($_GET['delete_kategoria'])) {
    $id = intval($_GET['delete_kategoria']);
    $sql = "DELETE FROM kategorie WHERE id = ? OR matka = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('ii', $id, $id);
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Kategoria została usunięta!</p>";
    } else {
        echo "<p style='color:red;'>Błąd: " . $link->error . "</p>";
    }
}

?>

<!-- Formularz dodawania kategorii -->
<h2>Dodaj kategorię</h2>
<form method="post">
    <label>Nazwa kategorii:</label>
    <input type="text" name="nazwa" required>
    <label>ID kategorii nadrzędnej (0 dla głównej):</label>
    <input type="number" name="matka" value="0">
    <button type="submit" name="dodajKategorie">Dodaj kategorię</button>
</form>

<!-- Wyświetlanie drzewa kategorii -->
<h2>Lista kategorii</h2>
<?php pokazKategorie($link); ?>

<hr>

<!-- Lista podstron (istniejące funkcjonalności) -->
<?php
function ListaPodstron($link) {
    echo '<h2>Lista podstron</h2>';
    $query = "SELECT id, page_title FROM page_list_";
    $result = $link->query($query);
    echo '<table border="1">';
    echo '<tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row['page_title']) . '</td>';
        echo '<td>';
        echo '<a href="admin.php?edit_id=' . $row['id'] . '">Edytuj</a> | ';
        echo '<a href="admin.php?delete_id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\');">Usuń</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

if (isset($_GET['edit_id'])) {
    EdytujPodstrone($link, intval($_GET['edit_id']));
} elseif (isset($_GET['delete_id'])) {
    UsunPodstrone($link, intval($_GET['delete_id']));
} elseif (isset($_GET['add'])) {
    DodajNowaPodstrone($link);
} else {
    ListaPodstron($link);
    echo '<br><a href="logout.php">Wyloguj się</a>';
}
?>
