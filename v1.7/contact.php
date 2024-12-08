<?php

// Funkcja wyświetlająca formularz kontaktowy
function PokazKontakt($message = '', $error = '') {
    echo '
    <div style="max-width: 500px; margin: 50px auto; padding: 20px; background-color: #f9f9f9; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
        <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 20px; text-align: center; color: #333;">Formularz Kontaktowy</h2>';
    
    if ($message) {
        echo '<div style="font-size: 14px; color: green; text-align: center; margin-bottom: 20px;">' . $message . '</div>';
    }
    if ($error) {
        echo '<div style="font-size: 14px; color: red; text-align: center; margin-bottom: 20px;">' . $error . '</div>';
    }

    echo '
        <form method="post" action="contact.php" style="display: flex; flex-direction: column; gap: 15px;">
            <label for="temat" style="font-size: 14px; font-weight: bold; color: #555;">Temat:</label>
            <input type="text" id="temat" name="temat" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; width: 100%;">

            <label for="tresc" style="font-size: 14px; font-weight: bold; color: #555;">Treść:</label>
            <textarea id="tresc" name="tresc" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; width: 100%; min-height: 100px;"></textarea>

            <label for="email" style="font-size: 14px; font-weight: bold; color: #555;">Email:</label>
            <input type="email" id="email" name="email" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; width: 100%;">

            <button type="submit" name="send" style="background-color: #007BFF; color: #fff; border: none; border-radius: 5px; padding: 10px 15px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background-color 0.3s ease;">Wyślij</button>
            <button type="submit" name="przypomnij" style="background-color: #007BFF; color: #fff; border: none; border-radius: 5px; padding: 10px 15px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background-color 0.3s ease;">Przypomnij hasło</button>
        </form>
    </div>';
}

// Funkcja wysyłająca e-mail z formularza kontaktowego
function WyslijMailKontakt($odbiorca) {
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        PokazKontakt('', 'Nie wypełniłeś wszystkich pól!');
        return;
    }

    $temat = htmlspecialchars($_POST['temat']);
    $tresc = htmlspecialchars($_POST['tresc']);
    $nadawca = htmlspecialchars($_POST['email']);

    $header = "From: Formularz kontaktowy <$nadawca>\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($odbiorca, $temat, $tresc, $header)) {
        PokazKontakt('Wiadomość została wysłana pomyślnie!', '');
    } else {
        PokazKontakt('', 'Wystąpił błąd podczas wysyłania wiadomości.');
    }
}

// Funkcja wysyłająca przypomnienie hasła
function PrzypomnijHaslo($odbiorca) {
    $temat = "Przypomnienie hasła";
    $haslo = "TwojeTajneHaslo123"; // Zastąp dynamicznie generowanym hasłem w prawdziwej aplikacji
    $tresc = "Twoje hasło do panelu administracyjnego to: $haslo";

    $header = "From: Admin <$odbiorca>\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($odbiorca, $temat, $tresc, $header)) {
        PokazKontakt('Hasło zostało wysłane na Twój adres e-mail.', '');
    } else {
        PokazKontakt('', 'Wystąpił błąd podczas wysyłania hasła.');
    }
}

// Obsługa zapytań POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send'])) {
        WyslijMailKontakt('odbiorca@domena.pl'); 
    } elseif (isset($_POST['przypomnij'])) {
        PrzypomnijHaslo('admin@domena.pl'); 
    }
} else {
    PokazKontakt();
}
?>
