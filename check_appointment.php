<?php
include 'config.php';

session_start();

$date = $_GET['date'];
$time = $_GET['time'];
$medic_id = $_SESSION['medic_id'];
$pacient_cnp = $_GET['pacient_cnp'];

// Verificăm dacă pacientul are deja o programare în aceeași zi
$sql = "SELECT * FROM programari WHERE data = ? AND pacient_CNP = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $date, $pacient_cnp);
$stmt->execute();
$result = $stmt->get_result();

$response = array('exists' => false);

if ($result->num_rows > 0) {
    $response['exists'] = true;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
