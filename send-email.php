<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Indirizzo di destinazione impostato
    $to = "info@lanternacorporation.com";
    $subject = "Nuova Candidatura - Lanterna Corporation";

    // Recupero dati inviati dal form
    $nome = strip_tags(trim($_POST["nome"] ?? ''));
    $cognome = strip_tags(trim($_POST["cognome"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $telefono = strip_tags(trim($_POST["telefono"] ?? ''));
    $citta = strip_tags(trim($_POST["citta"] ?? ''));
    $nascita = strip_tags(trim($_POST["nascita"] ?? ''));
    $messaggio = strip_tags(trim($_POST["messaggio"] ?? ''));

    // Generazione separatore per allegato MIME
    $boundary = md5(time());

    // Intestazioni Email
    $headers = "From: webmaster@lanternacorporation.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";

    // Testo del messaggio
    $body_text = "Nuova candidatura ricevuta dal sito web:\n\n";
    $body_text .= "Nome: $nome $cognome\n";
    $body_text .= "Email: $email\n";
    $body_text .= "Telefono: $telefono\n";
    $body_text .= "Città: $citta\n";
    $body_text .= "Data di nascita: $nascita\n\n";
    $body_text .= "Messaggio di presentazione:\n$messaggio\n";

    // Struttura messaggio
    $message = "--".$boundary."\r\n";
    $message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= $body_text . "\r\n";

    // Gestione allegato CV
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['cv']['tmp_name'];
        $file_name = $_FILES['cv']['name'];
        $file_size = $_FILES['cv']['size'];
        $file_type = $_FILES['cv']['type'];

        $handle = fopen($file_tmp, "r");
        $content = fread($handle, $file_size);
        fclose($handle);
        $encoded_content = chunk_split(base64_encode($content));

        $message .= "--".$boundary."\r\n";
        $message .= "Content-Type: ".$file_type."; name=\"".$file_name."\"\r\n";
        $message .= "Content-Disposition: attachment; filename=\"".$file_name."\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= $encoded_content . "\r\n";
    }

    $message .= "--".$boundary."--";

    // Invio mail
    if (mail($to, $subject, $message, $headers)) {
        http_response_code(200);
        echo "OK";
    } else {
        http_response_code(500);
        echo "Errore invio";
    }
}
?>