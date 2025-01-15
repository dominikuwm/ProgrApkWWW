<?php
session_start();  // Rozpoczęcie sesji
include('../cfg.php');  // Dołączenie konfiguracji z bazą danych

// Obsługa formularza logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['dodajKategorie']) && !isset($_POST['dodajProdukt'])) {
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
?>



<form method="post">
    <label>Nazwa kategorii:</label>
    <input type="text" name="nazwa" required>
    <label>ID kategorii nadrzędnej (0 dla głównej):</label>
    <input type="number" name="matka" value="0">
    <button type="submit" name="dodajKategorie">Dodaj kategorię</button>
</form>

<?php
// Funkcja wyświetlania kategorii w formie drzewa z obsługą edycji i usuwania
function pokazKategorie($link, $matka = 0, $poziom = 0) {
    $sql = "SELECT * FROM kategorie WHERE matka = ? ORDER BY nazwa";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('i', $matka);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        echo "<p style='color:red;'>Błąd SQL: " . $link->error . "</p>";
        return;
    }

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
    } else {
        echo "<p>Brak kategorii na tym poziomie.</p>";
    }
}

// Obsługa usuwania kategorii
if (isset($_GET['delete_kategoria'])) {
    $id = intval($_GET['delete_kategoria']);
    $sqlDelete = "DELETE FROM kategorie WHERE id = ? OR matka = ?";
    $stmtDelete = $link->prepare($sqlDelete);
    $stmtDelete->bind_param('ii', $id, $id);
    if ($stmtDelete->execute()) {
        echo "<p style='color:green;'>Kategoria oraz jej podkategorie zostały usunięte!</p>";
        header("Location: admin.php");
        exit;
    } else {
        echo "<p style='color:red;'>Błąd: " . $link->error . "</p>";
    }
}

// Wyświetlanie drzewa kategorii

pokazKategorie($link);

// Dodawanie kategorii
if (isset($_POST['dodajKategorie'])) {
    $nazwa = trim($_POST['nazwa']);
    $matka = (int)$_POST['matka'];

    if (empty($nazwa)) {
        echo "<p style='color:red;'>Nazwa kategorii nie może być pusta!</p>";
    } else {
        $sqlCheck = "SELECT * FROM kategorie WHERE nazwa = ? AND matka = ?";
        $stmtCheck = $link->prepare($sqlCheck);
        $stmtCheck->bind_param('si', $nazwa, $matka);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            echo "<p style='color:red;'>Kategoria o tej samej nazwie już istnieje na tym poziomie!</p>";
        } else {
            $sql = "INSERT INTO kategorie (matka, nazwa) VALUES (?, ?)";
            $stmt = $link->prepare($sql);
            $stmt->bind_param('is', $matka, $nazwa);
            if ($stmt->execute()) {
                echo "<p style='color:green;'>Kategoria została dodana!</p>";
                header("Refresh:0");  // Odświeżanie strony, aby pokazać nowe dane
                exit;
            } else {
                echo "<p style='color:red;'>Błąd: " . $link->error . "</p>";
            }
        }
    }
}

// -----------------------------------------
// Obsługa produktów
// -----------------------------------------

// Dodawanie produktu
if (isset($_POST['dodajProdukt'])) {
    $tytul = $_POST['tytul'];
    $opis = $_POST['opis'];
    $cena_netto = $_POST['cena_netto'];
    $podatek_vat = $_POST['podatek_vat'];
    $ilosc_sztuk = $_POST['ilosc_sztuk'];
    $status = isset($_POST['status_dostepnosci']) ? 1 : 0;
    $kategoria_id = $_POST['kategoria_id'];
    $gabaryt = $_POST['gabaryt'];
    $zdjecie = $_POST['zdjecie'];

    $sql = "INSERT INTO produkty (tytul, opis, cena_netto, podatek_vat, ilosc_sztuk, status_dostepnosci, kategoria, gabaryt, zdjecie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('ssddiisss', $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_sztuk, $status, $kategoria_id, $gabaryt, $zdjecie);
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Produkt został dodany!</p>";
    } else {
        echo "<p style='color:red;'>Błąd: " . $link->error . "</p>";
    }
}

echo '<h2>Lista produktów</h2>';
$query = "SELECT * FROM produkty";
$result = $link->query($query);
if ($result->num_rows > 0) {
    echo '<table border="1">';
    echo '<tr><th>ID</th><th>Tytuł</th><th>Cena netto</th><th>Ilość sztuk</th><th>Kategoria</th><th>Akcje</th></tr>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row['tytul']) . '</td>';
        echo '<td>' . htmlspecialchars($row['cena_netto']) . '</td>';
        echo '<td>' . htmlspecialchars($row['ilosc_sztuk']) . '</td>';
        echo '<td>' . htmlspecialchars($row['kategoria']) . '</td>';
        echo '<td>';
        echo '<a href="admin.php?edit_produkt=' . $row['id'] . '">Edytuj</a> | ';
        echo '<a href="admin.php?delete_produkt=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć ten produkt?\');">Usuń</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>Brak produktów w bazie.</p>';
}

// Usuwanie produktu
if (isset($_GET['delete_produkt'])) {
    $id = intval($_GET['delete_produkt']);
    $sql = "DELETE FROM produkty WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Produkt został usunięty!</p>";
    } else {
        echo "<p style='color:red;'>Błąd: " . $link->error . "</p>";
    }
}
?>

<!-- Formularz dodawania produktu -->
<h2>Dodaj produkt</h2>
<form method="post">
    <label>Tytuł produktu:</label>
    <input type="text" name="tytul" required><br>
    <label>Opis produktu:</label>
    <textarea name="opis"></textarea><br>
    <label>Cena netto:</label>
    <input type="number" name="cena_netto" step="0.01" required><br>
    <label>VAT (%):</label>
    <input type="number" name="podatek_vat" step="0.01" required><br>
    <label>Ilość sztuk:</label>
    <input type="number" name="ilosc_sztuk" required><br>
    <label>Status dostępności:</label>
    <input type="checkbox" name="status_dostepnosci"><br>
    <label>ID kategorii:</label>
    <input type="number" name="kategoria_id"><br>
    <label>Gabaryt (mały/średni/duży):</label>
    <input type="text" name="gabaryt"><br>
    <label>Link do zdjęcia:</label>
    <input type="text" name="zdjecie"><br>
    <button type="submit" name="dodajProdukt">Dodaj produkt</button>
</form>


<!-- Lista podstron (istniejące funkcjonalności) -->
<!-- Lista podstron -->
<?php
function ListaPodstron($link) {
    echo '<h2>Lista podstron</h2>';
    echo '<a href="admin.php?add=true" style="margin-bottom: 10px; display: inline-block;">Dodaj nową podstronę</a>'; // Link do formularza dodawania podstrony
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

// Funkcje CRUD dla podstron
function EdytujPodstrone($link, $id) {
    $query = "SELECT * FROM page_list_ WHERE id = ? LIMIT 1";
    $stmt = $link->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo '<h2>Edytuj podstronę</h2>';
    echo '<form method="post" action="admin.php?edit_id=' . $id . '">';
    echo '<label>Tytuł:</label>';
    echo '<input type="text" name="page_title" value="' . htmlspecialchars($row['page_title']) . '" required><br>';
    echo '<label>Treść:</label>';
    echo '<textarea name="page_content" rows="10">' . htmlspecialchars($row['page_content']) . '</textarea><br>';
    echo '<label>Aktywna:</label>';
    echo '<input type="checkbox" name="status" ' . ($row['status'] == 1 ? 'checked' : '') . '><br>';
    echo '<input type="submit" name="update_page" value="Zapisz zmiany">';
    echo '</form>';

    if (isset($_POST['update_page'])) {
        $title = htmlspecialchars($_POST['page_title']);
        $content = htmlspecialchars($_POST['page_content']);
        $status = isset($_POST['status']) ? 1 : 0;

        $update_query = "UPDATE page_list_ SET page_title = ?, page_content = ?, status = ? WHERE id = ?";
        $update_stmt = $link->prepare($update_query);
        $update_stmt->bind_param('ssii', $title, $content, $status, $id);
        $update_stmt->execute();

        header('Location: admin.php');
        exit;
    }
}

function DodajNowaPodstrone($link) {
    echo '<h2>Dodaj nową podstronę</h2>';
    echo '<form method="post" action="admin.php?add=true">';
    echo '<label>Tytuł:</label>';
    echo '<input type="text" name="page_title" required><br>';
    echo '<label>Treść:</label>';
    echo '<textarea name="page_content" rows="10"></textarea><br>';
    echo '<label>Aktywna:</label>';
    echo '<input type="checkbox" name="status"><br>';
    echo '<input type="submit" name="add_page" value="Dodaj podstronę">';
    echo '</form>';

    if (isset($_POST['add_page'])) {
        $title = htmlspecialchars($_POST['page_title']);
        $content = htmlspecialchars($_POST['page_content']);
        $status = isset($_POST['status']) ? 1 : 0;

        $insert_query = "INSERT INTO page_list_ (page_title, page_content, status) VALUES (?, ?, ?)";
        $stmt = $link->prepare($insert_query);
        $stmt->bind_param('ssi', $title, $content, $status);
        $stmt->execute();

        header('Location: admin.php');
        exit;
    }
}

function UsunPodstrone($link, $id) {
    $delete_query = "DELETE FROM page_list_ WHERE id = ?";
    $stmt = $link->prepare($delete_query);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: admin.php');
    exit;
}

?>

<hr>
<a href="logout.php">Wyloguj się</a>
