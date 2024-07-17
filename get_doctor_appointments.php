<?php
include 'config.php';
session_start();

$medic_id = $_GET['medic_id'];

$sql = "SELECT data, ora, nume_pacient, status FROM programari WHERE medic_id = ? AND status = 'APROBATA'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $medic_id);
$stmt->execute();
$result = $stmt->get_result();

$events = array();

while ($row = $result->fetch_assoc()) {
    $startTime = $row['data'] . 'T' . $row['ora'];
    $endTime = date("Y-m-d\TH:i:s", strtotime($startTime . ' + 30 minutes'));

    $events[] = array(
        'title' => $row['nume_pacient'],
        'start' => $startTime,
        'end' => $endTime,
        'status' => $row['status']
    );
}

$stmt->close();
$conn->close();

echo json_encode($events);
?>
