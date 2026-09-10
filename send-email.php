<?php
// Endpoint per la ricezione delle candidature dal form di candidati.html
// Hosting: Aruba "Hosting Basic Linux" (PHP 8) - invio tramite funzione mail()

// Accetta solo richieste POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    echo "Metodo non consentito";
    exit;
}

// Destinatario e mittente: DEVONO essere caselle realmente esistenti sul dominio,
// ospitate su Aruba. Se il mittente non e' una casella valida del dominio,
// Aruba rifiuta l'invio o la mail finisce in spam.
$to   = "info@lanternacorporation.com";
$from = "info@lanternacorporation.com";
$subject = "Nuova Candidatura - Lanterna Corporation";

// --- Recupero e pulizia dati ---
$nome      = trim($_POST["nome"] ?? '');
$cognome   = trim($_POST["cognome"] ?? '');
$email     = trim($_POST["email"] ?? '');
$telefono  = trim($_POST["telefono"] ?? '');
$citta     = trim($_POST["citta"] ?? '');
$nascita   = trim($_POST["nascita"] ?? '');
$messaggio = trim($_POST["messaggio"] ?? '');
$consenso  = isset($_POST["consenso"]);

// Rimuove eventuali tag HTML dai campi di testo
foreach (['nome', 'cognome', 'telefono', 'citta', 'nascita', 'messaggio'] as $campo) {
    $$campo = strip_tags($$campo);
}

// --- Validazione ---
$errori = [];

if ($nome === '' || $cognome === '' || $telefono === '' || $citta === '' || $nascita === '' || $messaggio === '') {
    $errori[] = "Compila tutti i campi obbligatori.";
}

// Email valida e senza caratteri utilizzabili per header injection (\r \n)
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $email)) {
    $errori[] = "Indirizzo email non valido.";
}

if (!$consenso) {
    $errori[] = "Devi accettare di essere contattato.";
}

// --- Validazione CV ---
$cv_ok = isset($_FILES['cv'])
    && $_FILES['cv']['error'] === UPLOAD_ERR_OK
    && is_uploaded_file($_FILES['cv']['tmp_name']);

if (!$cv_ok) {
    $errori[] = "Allega un CV valido.";
} else {
    $max_size  = 8 * 1024 * 1024; // 8 MB
    $ext       = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
    $ext_ammesse = ['pdf', 'doc', 'docx'];

    if ($_FILES['cv']['size'] > $max_size) {
        $errori[] = "Il CV supera la dimensione massima di 8 MB.";
    }
    if (!in_array($ext, $ext_ammesse, true)) {
        $errori[] = "Formato CV non ammesso (usa PDF, DOC o DOCX).";
    }
}

if (!empty($errori)) {
    http_response_code(422);
    echo implode(' ', $errori);
    exit;
}

// --- Costruzione email con allegato (multipart/mixed) ---
$boundary = md5(uniqid('', true));

$headers  = "From: Sito Lanterna Corporation <$from>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

$body_text  = "Nuova candidatura ricevuta dal sito web:\r\n\r\n";
$body_text .= "Nome: $nome $cognome\r\n";
$body_text .= "Email: $email\r\n";
$body_text .= "Telefono: $telefono\r\n";
$body_text .= "Citta: $citta\r\n";
$body_text .= "Data di nascita: $nascita\r\n\r\n";
$body_text .= "Messaggio di presentazione:\r\n$messaggio\r\n";

$message  = "--$boundary\r\n";
$message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$message .= $body_text . "\r\n";

// Allegato CV
$file_name    = preg_replace('/[\r\n"]/', '', $_FILES['cv']['name']);
$file_content = file_get_contents($_FILES['cv']['tmp_name']);
$encoded      = chunk_split(base64_encode($file_content));

$message .= "--$boundary\r\n";
$message .= "Content-Type: application/octet-stream; name=\"$file_name\"\r\n";
$message .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
$message .= "Content-Transfer-Encoding: base64\r\n\r\n";
$message .= $encoded . "\r\n";
$message .= "--$boundary--";

// Il 5o parametro (-f) imposta l'envelope sender: necessario su Aruba
// per il corretto allineamento SPF (il dominio ha "v=spf1 include:aruba.it").
$inviata = mail($to, $subject, $message, $headers, "-f $from");

if ($inviata) {
    http_response_code(200);
    echo "OK";
} else {
    http_response_code(500);
    echo "Errore durante l'invio. Riprova piu' tardi.";
}
