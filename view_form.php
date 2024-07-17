<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login-pacient.html");
    exit();
}

include 'config.php';

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    echo "Formularul nu a fost găsit.";
    exit();
}

$form_id = $_GET['id'];
$form_type = $_GET['type'];

// Determinarea tipului de utilizator și setarea parametrilor corespunzători
if (isset($_SESSION['medic_id'])) {
    $is_doctor = true;
    $user_id = $_SESSION['medic_id'];
} else {
    $is_doctor = false;
    $user_id = $_SESSION['cnp'];
}

if ($form_type == 'concediu') {
    $sql = $is_doctor ?
        "SELECT c.* FROM concedii_medicale c JOIN pacienti p ON c.cnp = p.CNP WHERE c.id = ? AND p.medic_id = ?" :
        "SELECT * FROM concedii_medicale WHERE id = ? AND cnp = ?";
} elseif ($form_type == 'trimitere') {
    $sql = $is_doctor ?
        "SELECT t.* FROM trimiteri t JOIN pacienti p ON t.cid_cnp_ce_pass = p.CNP WHERE t.id = ? AND p.medic_id = ?" :
        "SELECT * FROM trimiteri WHERE id = ? AND cid_cnp_ce_pass = ?";
} elseif ($form_type == 'reteta') {
    $sql = $is_doctor ?
        "SELECT r.* FROM retete_compensate r JOIN pacienti p ON r.cid_ce_pass = p.CNP WHERE r.id = ? AND p.medic_id = ?" :
        "SELECT * FROM retete_compensate WHERE id = ? AND cid_ce_pass = ?";
} else {
    echo "Tipul formularului nu este corect.";
    exit();
}

$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $form_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Formularul nu a fost găsit sau nu aveți permisiunea de a-l vedea.";
    exit();
}

$form_data = $result->fetch_assoc();

if ($form_type == 'concediu') {
    include 'concediu_template.php';
} elseif ($form_type == 'trimitere') {
    include 'trimitere_template.php';
} elseif ($form_type == 'reteta') {
    include 'reteta_template.php';
}

$stmt->close();
$conn->close();
?>
