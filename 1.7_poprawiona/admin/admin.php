<?php
session_start();  // Rozpoczęcie sesji
include('../cfg.php');  // Dołączenie konfiguracji z bazą danych

// Obsługa formularza logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    // Formularz logowania
    echo "
    <h2>Zaloguj się</h2>
    <form method='post'>
        <label>Login:</label>
        <input type='text' name='login' required><br>
        <label>Hasło:</label>
        <input type='password' name='password' required><br>
        <button type='submit'>Zaloguj się</button>
    </form>";
    exit;  // Zatrzymanie dalszego wyświetlania
}

// Panel administracyjny
echo '<h1>Witaj w panelu administratora!</h1>';

// Funkcja wyświetlająca listę podstron
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
        echo '<a href="admin.php?edit_id=' . $row['id'] . '">Edytuj</a>';
        echo ' | ';
        echo '<a href="admin.php?delete_id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\');">Usuń</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<br><a href="admin.php?add=true">Dodaj nową podstronę</a>';
}

// Funkcje obsługi CRUD
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

        $update_query = "UPDATE page_list_ SET page_title = ?, page_content = ?, status = ? WHERE id = ? LIMIT 1";
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
    $delete_query = "DELETE FROM page_list_ WHERE id = ? LIMIT 1";
    $stmt = $link->prepare($delete_query);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: admin.php');
    exit;
}

// Obsługa akcji w panelu admina
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
