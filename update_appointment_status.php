<?php
include 'config.php';
session_start();

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON decode error: ' . json_last_error_msg());
    }

    $id = $data['id'];
    $action = $data['action'];
    $response = array('success' => false);

    // Mesaj de debug pentru a verifica dacă datele sunt primite corect
    error_log("Received data: ID = $id, Action = $action");

    // Verificăm informațiile despre programare
    $sql = "SELECT * FROM programari WHERE id = ?"; // Obținem detaliile programării
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $programare = $result->fetch_assoc();

    if (!$programare) {
        throw new Exception('Programarea nu a fost găsită');
    }

    $pacient_cnp = $programare['pacient_CNP'];
    $nume_pacient = $programare['nume_pacient'];
    $medic_id = $programare['medic_id'];

    $conn->begin_transaction(); // Începem tranzacția

    if ($action === 'approve') {
        $sql = "UPDATE programari SET status = 'APROBATA' WHERE id = ?"; // Aprobare programare
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            // Inserăm notificarea pentru pacient
            $notificare_msg = "Programarea dvs. din data de " . $programare['data'] . " la ora " . $programare['ora'] . " a fost confirmata!";
            $sql_notificare = "INSERT INTO notificari (programare_id, mesaj, destinatar) VALUES (?, ?, 'pacient')";
            $stmt_notificare = $conn->prepare($sql_notificare);
            if (!$stmt_notificare) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }
            $stmt_notificare->bind_param('is', $id, $notificare_msg);
            if (!$stmt_notificare->execute()) {
                throw new Exception('Execute failed: ' . $stmt_notificare->error);
            }
            $stmt_notificare->close();

            $response['success'] = true;
        } else {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $stmt->close();
    } elseif ($action === 'delete') {
        // Schimbăm statusul programării în 'RESPINSA'
        $sql = "UPDATE programari SET status = 'RESPINSA' WHERE id = ?"; // Schimbare status în RESPINSA
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            // Inserăm notificarea pentru pacient
            $notificare_msg = "Solicitarea dvs. din data de " . $programare['data'] . " la ora " . $programare['ora'] . " a fost respinsa! Luati legatura cu medicul asistent!";
            $sql_notificare = "INSERT INTO notificari (programare_id, mesaj, destinatar) VALUES (?, ?, 'pacient')";
            $stmt_notificare = $conn->prepare($sql_notificare);
            if (!$stmt_notificare) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }
            $stmt_notificare->bind_param('is', $id, $notificare_msg);
            if (!$stmt_notificare->execute()) {
                throw new Exception('Execute failed: ' . $stmt_notificare->error);
            }
            $stmt_notificare->close();

            $response['success'] = true;
        } else {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception("Unknown action: $action");
    }

    $conn->commit(); // Confirmăm tranzacția
    $conn->close();
} catch (Exception $e) {
    $conn->rollback(); // Anulăm tranzacția în caz de eroare
    error_log("Error: " . $e->getMessage());
    $response['error'] = $e->getMessage();
    echo json_encode($response);
    exit();
}

// Mesaj de debug pentru a verifica răspunsul
error_log("Response: " . json_encode($response));

echo json_encode($response);
?>
