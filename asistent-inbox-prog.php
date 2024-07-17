<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$prenume = $_SESSION['user'];
$cnp = $_SESSION['cnp'];
$medic_id = $_SESSION['medic_id'];

require_once 'config.php';

// Selectarea notificărilor pentru asistent
$sql = "SELECT n.id, n.mesaj, n.data_trimiterii, p.nume_pacient, p.data, p.ora, n.citit
        FROM notificari n
        JOIN programari p ON n.programare_id = p.id
        WHERE n.destinatar = 'asistent' AND p.medic_id = ?
        ORDER BY n.data_trimiterii DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $medic_id);
$stmt->execute();
$result = $stmt->get_result();

$notificari = [];
while ($row = $result->fetch_assoc()) {
    $notificari[] = $row;
    if ($row['citit'] == 0) {
        // Marcarea notificării ca citită
        $updateSql = "UPDATE notificari SET citit = 1 WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param('i', $row['id']);
        $updateStmt->execute();
        $updateStmt->close();
    }
}
$stmt->close();

// Obținerea numărului de notificări necitite
$sql = "SELECT COUNT(*) as unread_count 
        FROM notificari n
        JOIN programari p ON n.programare_id = p.id
        WHERE p.medic_id = ? AND n.destinatar = 'asistent' AND n.citit = 0";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('i', $medic_id);
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$result = $stmt->get_result();
if (!$result) {
    die("Get result failed: " . $stmt->error);
}
$row = $result->fetch_assoc();
$unread_notifications = $row['unread_count'];
$stmt->close();

// Obținerea numărului de mesaje necitite din tabelul mesaje
$sql = "SELECT COUNT(*) as unread_count 
        FROM mesaje 
        WHERE medic_id = ? AND destinatar = 'asistent' AND citit = 0";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('i', $medic_id);
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$result = $stmt->get_result();
if (!$result) {
    die("Get result failed: " . $stmt->error);
}
$row = $result->fetch_assoc();
$unread_messages = $row['unread_count'];
$stmt->close();

// Calcularea numărului total de notificări necitite
$total_unread = $unread_notifications + $unread_messages;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style/inbox.css">
  <title>Inbox Asistent</title>
  <style>
    .new-message {
      color: red;
      font-style: italic;
      float: right;
    }
    .date-header {
      font-weight: bold;
      margin-top: 20px;
      margin-bottom: 10px;
    }
    .inbox-notification {
      display: block;
      margin-bottom: 10px;
      padding: 10px;
      background: #f9f9f9;
      text-decoration: none;
      color: black;
    }
    .inbox-notification:hover {
      background: #eee;
    }
    .inbox-notification.unread {
      font-weight: bold;
    }
    .button-container {
        text-align: center;
        margin: 20px 0;
    }
    .button-container button {
        background-color: #ccc;
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
    }
    .button-container .active {
        background-color: #555;
    }
    .inbox-container {
        max-height: 80vh; /* Limităm înălțimea containerului */
        overflow-y: auto; /* Adăugăm scrollbar dacă este necesar */
    }
    .sticky-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 1000;
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
    }
    
  </style>
</head>
<body>
<div id="sidebar">
    <ul>
      <li><a href="#"><i class="fa fa-user fa-sm"></i> Log Out</a></li>
    </ul>
  </div>

  <nav id="navbar" class="navsticky">
    <div class="container">
      <h1 class="logo"><a href="index.html">MedFM</a></h1>
      <ul>
      <li><a href="asistent-home.php">Calendar</a></li>
        <li class="navbar-item">
            
          <a href="asistent-inbox.php">Inbox</a>
          <?php if ($total_unread > 0): ?>
            <span class="notification-badge"><?php echo $total_unread; ?></span>
          <?php endif; ?>
        </li>

        <li><a href="#">Bună, <?php echo htmlspecialchars($prenume); ?></a></li>
      </ul>
    </div>
  </nav>
  <div class="container container-home">
    <div class="inbox-container">
      <div class="sticky-header">
        <h2 style="text-align: center;">NOTIFICĂRILE MELE</h2>
        <div class="button-container">
          <button class="btn btn-programari" onclick="location.href='asistent-inbox.php'">Mesaje <?php if ($unread_messages > 0) echo "($unread_messages)"; ?></button>
          <button class="active btn btn-mesaje" style="background-color: #618264;" onclick="location.href='#'">Programari <?php if ($unread_notifications > 0) echo "($unread_notifications)"; ?></button>
        </div>
      </div>
      <?php if (empty($notificari)): ?>
        <p style="text-align: center;">Nu aveți nicio notificare.</p>
      <?php else: ?>
        <?php foreach ($notificari as $notificare): ?>
          <div class="inbox-notification <?php echo $notificare['citit'] == 0 ? 'unread' : ''; ?>">
            <?php echo htmlspecialchars($notificare['mesaj']); ?>
            <br>
            <span><?php echo htmlspecialchars($notificare['data_trimiterii']); ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
  <footer id="secondary-footer">
    <p>MedFM &copy; 2023, Toate drepturile rezervate</p>
  </footer>
</body>
</html>
