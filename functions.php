<?php
require_once 'config.php';

function getUserByCNP($cnp) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM conturi WHERE username = ?");
    $stmt->bind_param("s", $cnp);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getMedicIdByCNP($cnp) {
    global $conn;
    $stmt = $conn->prepare("SELECT medic_id FROM pacienti WHERE CNP = ?");
    $stmt->bind_param("s", $cnp);
    $stmt->execute();
    $stmt->bind_result($medic_id);
    $stmt->fetch();
    return $medic_id;
}

function getAsistentByMedicId($medic_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT nume, prenume FROM asistenti WHERE medic_id = ?");
    $stmt->bind_param("i", $medic_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function insertMessage($medic_id, $nume, $prenume, $cnp, $mesaj) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO mesaje (medic_id, nume, prenume, cnp, mesaj) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $medic_id, $nume, $prenume, $cnp, $mesaj);
    $stmt->execute();
}

function getMessages($cnp, $medic_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM mesaje WHERE cnp = ? OR medic_id = ? ORDER BY data_trimiterii ASC");
    $stmt->bind_param("si", $cnp, $medic_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>
