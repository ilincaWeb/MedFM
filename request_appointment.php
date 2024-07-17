<?php
include 'config.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);

$date = $data['date'];
$time = $data['time'];
$pacient_cnp = $data['pacient_cnp'];
$nume_pacient = $data['nume_pacient'];
$medic_id = $_SESSION['medic_id'];

// Inserăm solicitarea de programare
$sql = "INSERT INTO programari (data, ora, pacient_CNP, nume_pacient, status, medic_id) VALUES (?, ?, ?, ?, 'SOLICITATA', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssi', $date, $time, $pacient_cnp, $nume_pacient, $medic_id);

$response = array();

if ($stmt->execute()) {
    $programare_id = $stmt->insert_id;

    // Inserăm notificarea pentru asistent
    $notificare_msg_asistent = "Programare solicitata de $nume_pacient";
    $sql_notificare_asistent = "INSERT INTO notificari (programare_id, mesaj, destinatar) VALUES (?, ?, 'asistent')";
    $stmt_notificare_asistent = $conn->prepare($sql_notificare_asistent);
    $stmt_notificare_asistent->bind_param('is', $programare_id, $notificare_msg_asistent);
    $stmt_notificare_asistent->execute();
    $stmt_notificare_asistent->close();

    // Inserăm notificarea pentru pacient
    $notificare_msg_pacient = "Solicitarea dvs. a fost trimisă cu succes!";
    $sql_notificare_pacient = "INSERT INTO notificari (programare_id, mesaj, destinatar) VALUES (?, ?, 'pacient')";
    $stmt_notificare_pacient = $conn->prepare($sql_notificare_pacient);
    $stmt_notificare_pacient->bind_param('is', $programare_id, $notificare_msg_pacient);
    $stmt_notificare_pacient->execute();
    $stmt_notificare_pacient->close();

    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['error'] = $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
