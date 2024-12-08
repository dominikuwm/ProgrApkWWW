<?php
session_start();
include('../cfg.php');

// Funkcja do wyświetlenia formularza logowania
function FormularzLogowania() {
    $wynik = '
    <div class="logowanie" style="max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">
        <h1 class="heading" style="text-align: center; font-family: Arial, sans-serif;">Panel CMS</h1>
        <form method="post" name="loginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
            <table class="logowanie" style="width: 100%; margin-top: 10px;">
                <tr><td class="log4_t" style="padding-bottom: 10px; font-family: Arial, sans-serif;">Email:</td><td><input type="text" name="login_email" class="logowanie" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;" /></td></tr>
                <tr><td class="log4_t" style="padding-bottom: 10px; font-family: Arial, sans-serif;">Hasło:</td><td><input type="password" name="login_pass" class="logowanie" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;" /></td></tr>
                <tr><td>&nbsp;</td><td style="text-align: right;"><input type="submit" name="x1_submit" class="logowanie" value="Zaloguj" style="padding: 10px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;" /></td></tr>
            </table>
        </form>
    </div>
    ';
    return $wynik;
}

// Sprawdzenie, czy formularz logowania został wysłany
if (isset($_POST['x1_submit'])) {
    $login = $_POST['login_email'];
    $password = $_POST['login_pass'];

    // Pobierz dane z cfg.php
    if ($login == $cfg_login && $password == $cfg_pass) {
        // Zalogowanie użytkownika
        $_SESSION['zalogowany'] = true;
    } else {
        echo '<p style="color: red; text-align: center;">Nieprawidłowy login lub hasło.</p>';
        echo FormularzLogowania();
        exit();
    }
}

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
    echo FormularzLogowania();
    exit();
}

// Kod administracyjny - dostępny tylko dla zalogowanych użytkowników
echo '<div style="max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">';
echo '<h2 style="text-align: center; font-family: Arial, sans-serif;">Witaj w panelu administratora!</h2>';
echo '<div style="text-align: center; margin-bottom: 20px;"><a href="admin.php?akcja=dodaj" style="padding: 10px 20px; background-color: #28a745; color: #fff; border: none; border-radius: 5px; text-decoration: none; cursor: pointer;">Dodaj nową podstronę</a></div>';

// Funkcja do usuwania podstrony
function UsunPodstrone($id) {
    include('../cfg.php');

    $query = "DELETE FROM page_list WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo '<p style="color: green;">Podstrona została usunięta.</p>';
    } else {
        echo '<p style="color: red;">Błąd podczas usuwania podstrony: ' . $conn->error . '</p>';
    }
}

// Funkcja do dodawania nowej podstrony
function DodajNowaPodstrone() {
    include('../cfg.php');

    if (isset($_POST['add'])) {
        $title = $_POST['page_title'];
        $content = $_POST['page_content'];
        $status = isset($_POST['status']) ? 1 : 0;

        $query = "INSERT INTO page_list (page_title, page_content, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ssi", $title, $content, $status);
            $stmt->execute();
            echo '<p style="color: green;">Nowa podstrona została dodana.</p>';
        } else {
            echo '<p style="color: red;">Błąd podczas dodawania podstrony: ' . $conn->error . '</p>';
        }
    }

    echo '<div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">';
    echo '<form method="POST" action="">
            <label style="font-family: Arial, sans-serif;">Tytuł: <input type="text" name="page_title" style="width: 100%; padding: 8px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px;"></label><br>
            <label style="font-family: Arial, sans-serif; margin-top: 10px;">Treść: <textarea name="page_content" style="width: 100%; padding: 8px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea></label><br>
            <label style="font-family: Arial, sans-serif; margin-top: 10px;">Aktywna: <input type="checkbox" name="status"></label><br>
            <input type="submit" name="add" value="Dodaj Podstronę" style="margin-top: 20px; padding: 10px 20px; background-color: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
          </form>';
    echo '</div>';
}

// Funkcja do edytowania podstrony
function EdytujPodstrone($id) {
    include('../cfg.php');

    if (isset($_POST['update'])) {
        $new_title = $_POST['page_title'];
        $new_content = $_POST['page_content'];
        $new_status = isset($_POST['status']) ? 1 : 0;

        $query = "UPDATE page_list SET page_title = ?, page_content = ?, status = ? WHERE id = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ssii", $new_title, $new_content, $new_status, $id);
            $stmt->execute();
            echo '<p style="color: green;">Podstrona została zaktualizowana.</p>';
        } else {
            echo '<p style="color: red;">Błąd podczas edytowania podstrony: ' . $conn->error . '</p>';
        }
    }

    $query = "SELECT * FROM page_list WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        echo '<div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">';
        echo '<form method="POST" action="">
                <label style="font-family: Arial, sans-serif;">Tytuł: <input type="text" name="page_title" value="' . htmlspecialchars($row['page_title']) . '" style="width: 100%; padding: 8px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px;"></label><br>
                <label style="font-family: Arial, sans-serif; margin-top: 10px;">Treść: <textarea name="page_content" style="width: 100%; padding: 8px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px;">' . htmlspecialchars($row['page_content']) . '</textarea></label><br>
                <label style="font-family: Arial, sans-serif; margin-top: 10px;">Aktywna: <input type="checkbox" name="status"' . ($row['status'] ? ' checked' : '') . '></label><br>
                <input type="submit" name="update" value="Zaktualizuj" style="margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
              </form>';
        echo '</div>';
    } else {
        echo '<p style="color: red;">Błąd podczas pobierania danych podstrony: ' . $conn->error . '</p>';
    }
}

// Funkcja do wyświetlenia listy podstron
function ListaPodstron() {
    include('../cfg.php');
    $query = "SELECT id, page_title FROM page_list";
    $result = $conn->query($query);

    if ($result) {
        echo '<div style="max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">';
        echo '<table border="1" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">';
        echo '<tr style="background-color: #007bff; color: #fff;"><th style="padding: 10px;">ID</th><th style="padding: 10px;">Tytuł</th><th style="padding: 10px;">Akcje</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr style="border-bottom: 1px solid #ccc;">';
            echo '<td style="padding: 10px; text-align: center;">' . $row['id'] . '</td>';
            echo '<td style="padding: 10px;">' . htmlspecialchars($row['page_title']) . '</td>';
            echo '<td style="padding: 10px; text-align: center;">
                    <a href="admin.php?akcja=edytuj&id=' . $row['id'] . '" style="color: #007bff; text-decoration: none;">Edytuj</a> |
                    <a href="admin.php?akcja=usun&id=' . $row['id'] . '" style="color: #dc3545; text-decoration: none;" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\');""">Usuń</a>
                  </td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
    } else {
        echo '<p style="color: red;">Błąd podczas wyświetlania listy podstron: ' . $conn->error . '</p>';
    }
}

// Logika obsługi akcji
if (isset($_GET['akcja'])) {
    $akcja = $_GET['akcja'];
    if ($akcja == 'usun' && isset($_GET['id'])) {
        UsunPodstrone((int)$_GET['id']);
    } elseif ($akcja == 'edytuj' && isset($_GET['id'])) {
        EdytujPodstrone((int)$_GET['id']);
    } elseif ($akcja == 'dodaj') {
        DodajNowaPodstrone();
    } else {
        ListaPodstron();
    }
} else {
    ListaPodstron();
}
?>
