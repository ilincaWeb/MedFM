<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login_doctor.html");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

$medic_id = $_SESSION['medic_id']; // Preia medic_id din sesiune

// Preluarea datelor din formular
$nume = $_POST['nume'];
$prenume = $_POST['prenume'];
$sex = $_POST['sex'];
$data_nasterii = $_POST['data_nasterii'];
$cnp = $_POST['CNP'];
$varsta = $_POST['varsta'];
$greutate = isset($_POST['greutate']) ? $_POST['greutate'] : null;
$inaltime = isset($_POST['inaltime']) ? $_POST['inaltime'] : null;
$stare_civila = isset($_POST['stare_civila']) ? $_POST['stare_civila'] : null;
$ocupatie = isset($_POST['ocupatie']) ? $_POST['ocupatie'] : null;
$reactii_adverse = isset($_POST['reactii_adverse']) ? $_POST['reactii_adverse'] : null;

// Inserarea datelor în baza de date
$sql = "INSERT INTO pacienti (medic_id, nume, prenume, sex, data_nasterii, CNP, varsta, greutate, inaltime, stare_civila, ocupatie, reactii_adverse) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssssiissss", $medic_id, $nume, $prenume, $sex, $data_nasterii, $cnp, $varsta, $greutate, $inaltime, $stare_civila, $ocupatie, $reactii_adverse);

if ($stmt->execute() === TRUE) {
    $_SESSION['pacient_message'] = "New patient added successfully";
    $_SESSION['pacient_message_type'] = "success";
} else {
    $_SESSION['pacient_message'] = "Error: " . $stmt->error;
    $_SESSION['pacient_message_type'] = "error";
}

$stmt->close();
$conn->close();

header("Location: inregistrare_pacient.php");
exit();
?>
