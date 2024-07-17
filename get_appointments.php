<?php
include 'config.php';
session_start();

try {
    if (!isset($_SESSION['medic_id'])) {
        throw new Exception("Medic ID is not set in the session.");
    }

    $medic_id = $_SESSION['medic_id'];
    $user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

    // Excludem programările cu statusul 'RESPINSA'
    $sql = "SELECT id, data, TIME_FORMAT(ora, '%H:%i') as ora, nume_pacient, status, pacient_CNP FROM programari WHERE medic_id = ? AND status != 'RESPINSA'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param('i', $medic_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $events = array();
    $solicitari_count = array();

    while ($row = $result->fetch_assoc()) {
        // Calculăm ora de sfârșit la 30 de minute după ora de început
        $startTime = $row['data'] . 'T' . $row['ora'];
        $endTime = date("Y-m-d\TH:i", strtotime($startTime . ' + 30 minutes'));

        // Contorizăm solicitările pentru aceeași dată și oră
        $key = $row['data'] . ' ' . $row['ora'];
        if (!isset($solicitari_count[$key])) {
            $solicitari_count[$key] = 0;
        }
        $solicitari_count[$key]++;

        // Setăm titlul pentru evenimentele aprobate
        $title = $row['nume_pacient'];
        if ($user_type === 'pacient' && $row['status'] === 'APROBATA' && $row['pacient_CNP'] !== $_SESSION['cnp']) {
            $title = 'INDISPONIBIL';
        }

        $events[] = array(
            'id' => $row['id'],
            'title' => $title,
            'start' => $startTime,
            'end' => $endTime,
            'status' => $row['status'],
            'count' => $solicitari_count[$key],
            'pacient_cnp' => $row['pacient_CNP'], // Adăugăm pacient_CNP în răspuns
            'nume_pacient' => $row['nume_pacient'], // Adăugăm nume_pacient în răspuns
            'ora' => $row['ora'] // Adăugăm ora în răspuns
        );
    }

    $stmt->close();
    $conn->close();

    echo json_encode($events);
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(array('error' => 'Internal Server Error', 'message' => $e->getMessage()));
}
?>
