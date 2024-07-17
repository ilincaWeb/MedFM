<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login_doctor.html");
    exit();
}

include 'config.php';

// Obține CNP-ul medicului din sesiune
$medic_cnp = $_SESSION['username'];

// Obține id-ul medicului din tabela medici folosind CNP-ul din sesiune
$stmt = $conn->prepare("SELECT id FROM medici WHERE CNP = ?");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $medic_cnp);
$stmt->execute();
$stmt->bind_result($medic_id);
$stmt->fetch();
$stmt->close();

if (!$medic_id) {
    $_SESSION['message'] = "Eroare la identificarea medicului cu CNP-ul din sesiune.";
    $_SESSION['message_type'] = "error";
    header("Location: stergere_pacient.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cnp_pacient = $_POST['cnp'];

    // Verifică dacă pacientul aparține medicului conectat
    $stmt = $conn->prepare("SELECT medic_id FROM pacienti WHERE CNP = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $cnp_pacient);
    $stmt->execute();
    $stmt->bind_result($pacient_medic_id);
    $stmt->fetch();
    $stmt->close();

    if ($pacient_medic_id == $medic_id) {
        // Șterge pacientul
        $stmt = $conn->prepare("DELETE FROM pacienti WHERE CNP = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $cnp_pacient);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Pacientul a fost șters cu succes.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Eroare la ștergerea pacientului.";
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Pacientul nu aparține medicului conectat.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: stergere_pacient.php");
    exit();
}

$conn->close();
?>
