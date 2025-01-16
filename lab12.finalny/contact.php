<?php

// Funkcja do wyświetlenia formularza kontaktowego
function PokazKontakt() {
    $form = '
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 10px; background-color: #1e1e2f; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);">
        <h2 style="text-align: center; font-family: Arial, sans-serif; color: #ffffff;">Formularz Kontaktowy</h2>
        <form method="post" action="">
            <label for="name" style="font-family: Arial, sans-serif; color: #ddd;">Imię:</label>
            <input type="text" name="name" id="name" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #555; border-radius: 5px; background-color: #2a2a40; color: #fff;" required><br><br>
            
            <label for="email" style="font-family: Arial, sans-serif; color: #ddd;">E-mail:</label>
            <input type="email" name="email" id="email" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #555; border-radius: 5px; background-color: #2a2a40; color: #fff;" required><br><br>
            
            <label for="message" style="font-family: Arial, sans-serif; color: #ddd;">Wiadomość:</label>
            <textarea name="message" id="message" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #555; border-radius: 5px; background-color: #2a2a40; color: #fff;" rows="5" required></textarea><br><br>
            
            <button type="submit" name="send" style="padding: 12px 20px; background-color: #4caf50; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Wyślij</button>
        </form>
        <br>
        <h3 style="text-align: center; font-family: Arial, sans-serif; color: #ffffff;">Przypomnienie hasła</h3>
        <form method="post" action="">
            <label for="remind-email" style="font-family: Arial, sans-serif; color: #ddd;">Wprowadź adres e-mail do przypomnienia hasła:</label>
            <input type="email" name="email" id="remind-email" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #555; border-radius: 5px; background-color: #2a2a40; color: #fff;" required><br><br>
            <button type="submit" name="remind" style="padding: 12px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Przypomnij hasło</button>
        </form>
    </div>
    ';
    echo $form;
}

// Funkcja do wysyłania maila przez formularz kontaktowy
function WyslijMailKontakt() {
    if (empty($_POST['name']) || empty($_POST['message']) || empty($_POST['email'])) {
        echo '<p style="color: red; text-align: center;">Wypełnij wszystkie pola formularza!</p>';
        PokazKontakt();
        return;
    }

    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $recipient = "admin@example.com"; // Podmień na rzeczywisty adres e-mail administratora
    $subject = "Wiadomość z formularza kontaktowego od: $name";
    $headers = "From: Formularz Kontaktowy <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $body = "Imię: $name\n";
    $body .= "E-mail: $email\n\n";
    $body .= "Wiadomość:\n$message\n";

    if (mail($recipient, $subject, $body, $headers)) {
        echo '<p style="color: green; text-align: center;">Wiadomość została wysłana pomyślnie!</p>';
    } else {
        echo '<p style="color: red; text-align: center;">Błąd podczas wysyłania wiadomości. Spróbuj ponownie później.</p>';
    }
}

// Funkcja do przypomnienia hasła (na potrzeby ćwiczenia)
function PrzypomnijHaslo() {
    if (empty($_POST['email'])) {
        echo '<p style="color: red; text-align: center;">Wprowadź adres e-mail!</p>';
        return;
    }

    $email = htmlspecialchars($_POST['email']);
    $recipient = "admin@example.com"; // Adres e-mail administratora lub systemu
    $subject = "Przypomnienie hasła";
    $headers = "From: System CMS <no-reply@example.com>\r\n";
    $headers .= "Reply-To: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $body = "Żądanie przypomnienia hasła dla użytkownika o adresie e-mail: $email.\n";
    $body .= "Sprawdź bazę danych, aby zresetować hasło dla użytkownika.\n";

    if (mail($recipient, $subject, $body, $headers)) {
        echo '<p style="color: green; text-align: center;">Wysłano prośbę o przypomnienie hasła. Sprawdź swoją skrzynkę e-mail.</p>';
    } else {
        echo '<p style="color: red; text-align: center;">Błąd podczas wysyłania wiadomości. Spróbuj ponownie później.</p>';
    }
}

// Logika obsługi formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send'])) {
        WyslijMailKontakt();
    } elseif (isset($_POST['remind'])) {
        PrzypomnijHaslo();
    }
} else {
    PokazKontakt();
}

?>
