<?php
require_once 'config.php';

// Obține data curentă și data de mâine
$current_date = date('Y-m-d');
$tomorrow_date = date('Y-m-d', strtotime('+1 day'));

// Selectarea programărilor pentru notificări
$sql = "SELECT p.id as programare_id, p.data, p.ora, p.pacient_CNP
        FROM programari p
        WHERE p.data IN (?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('ss', $current_date, $tomorrow_date);
$stmt->execute();
$result = $stmt->get_result();

$notificari = [];
while ($row = $result->fetch_assoc()) {
    $notificare = [];
    $notificare['programare_id'] = $row['programare_id'];
    $notificare['pacient_CNP'] = $row['pacient_CNP'];
    $notificare['data'] = $row['data'];
    $notificare['ora'] = date('H:i', strtotime($row['ora'])); // Formatează ora în hh:mm

    if ($row['data'] == $current_date) {
        $notificare['mesaj'] = "Nu uitati ca azi aveti programare la ora {$notificare['ora']}!";
    } elseif ($row['data'] == $tomorrow_date) {
        $notificare['mesaj'] = "Nu uitati ca maine aveti programare la ora {$notificare['ora']}!";
    }

    // Verifică dacă notificarea există deja în baza de date
    $check_sql = "SELECT id FROM notificari WHERE programare_id = ? AND mesaj = ? AND destinatar = 'pacient'";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $check_stmt->bind_param('is', $notificare['programare_id'], $notificare['mesaj']);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows == 0) {
        $notificari[] = $notificare;
    }

    $check_stmt->close();
}
$stmt->close();

// Introducerea notificărilor în baza de date
foreach ($notificari as $notificare) {
    $sql = "INSERT INTO notificari (programare_id, mesaj, destinatar, citit) VALUES (?, ?, 'pacient', 0)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('is', $notificare['programare_id'], $notificare['mesaj']);
    $stmt->execute();
    if ($stmt->error) {
        die('Execute failed: ' . $stmt->error);
    }
    $stmt->close();
}
?>
