<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preia datele din formular
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Validare date (opțional)
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $_SESSION['contact_message'] = "Toate câmpurile sunt obligatorii.";
        $_SESSION['contact_message_type'] = "error";
        header("Location: contact.php");
        exit();
    }

    // Conectare la baza de date
    require_once 'config.php';

    // Pregătește și execută inserarea în baza de date
    $sql = "INSERT INTO contact (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['contact_message'] = "Prepare statement failed: " . $conn->error;
        $_SESSION['contact_message_type'] = "error";
        header("Location: contact.php");
        exit();
    }
    $stmt->bind_param('ssss', $name, $email, $subject, $message);
    if ($stmt->execute() === TRUE) {
        $_SESSION['contact_message'] = "Mesajul a fost trimis cu succes.";
        $_SESSION['contact_message_type'] = "success";
    } else {
        $_SESSION['contact_message'] = "Error: " . $stmt->error;
        $_SESSION['contact_message_type'] = "error";
    }

    // Închide statement-ul și conexiunea
    $stmt->close();
    $conn->close();

    // Redirecționează utilizatorul înapoi la pagina de contact
    header("Location: contact.php");
    exit();
} else {
    // Dacă formularul nu a fost trimis corect, redirecționează înapoi la pagina de contact
    header("Location: contact.php");
    exit();
}
?>
