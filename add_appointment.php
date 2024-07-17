<?php
include 'config.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);

$date = $data['date'];
$time = $data['time'];
$prenume = $data['prenume'];
$nume = $data['nume'];
$medic_id = $data['medic_id'];
$pacient_nume_complet = $prenume . ' ' . $nume;

// Obținem medic_id și numele medicului pe baza sesiunii
$sql = "SELECT medici.id, medici.nume, medici.prenume FROM medici JOIN asistenti ON medici.id = asistenti.medic_id WHERE asistenti.CNP = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $_SESSION['cnp']);
$stmt->execute();
$result = $stmt->get_result();
$medic_data = $result->fetch_assoc();
$stmt->close();

$medic_id = $medic_data['id'];
$medic_nume_complet = $medic_data['prenume'] . ' ' . $medic_data['nume'];

// Obținem CNP-ul și medic_id-ul pacientului pe baza numelui și prenumelui
$sql = "SELECT CNP, medic_id FROM pacienti WHERE nume = ? AND prenume = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $nume, $prenume);
$stmt->execute();
$result = $stmt->get_result();
$pacient_data = $result->fetch_assoc();
$stmt->close();

$response = array('success' => false);

if ($pacient_data) {
    if ($pacient_data['medic_id'] == $medic_id) {
        $sql = "INSERT INTO programari (data, ora, pacient_CNP, nume_pacient, status, medic_id) VALUES (?, ?, ?, ?, 'APROBATA', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $date, $time, $pacient_data['CNP'], $pacient_nume_complet, $medic_id);
        
        if ($stmt->execute()) {
            // Obține ID-ul programării inserate
            $programare_id = $stmt->insert_id;

            // Inserează notificarea pentru pacient
            $notification_sql = "INSERT INTO notificari (programare_id, mesaj, destinatar, citit) VALUES (?, ?, 'pacient', 0)";
            $notification_stmt = $conn->prepare($notification_sql);
            $mesaj = "Aveți o programare nouă pe data de $date la ora $time.";
            $notification_stmt->bind_param('is', $programare_id, $mesaj);
            
            if ($notification_stmt->execute()) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Error: ' . $notification_stmt->error;
            }

            $notification_stmt->close();
        } else {
            $response['message'] = 'Error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "Pacientul nu apartine de medicul $medic_nume_complet.";
        $_SESSION['message_type'] = 'error';
        $response['message'] = $_SESSION['message'];
    }
} else {
    $_SESSION['message'] = "Pacientul nu exista in baza de date.";
    $_SESSION['message_type'] = 'error';
    $response['message'] = $_SESSION['message'];
}

$conn->close();

echo json_encode($response);
?>
