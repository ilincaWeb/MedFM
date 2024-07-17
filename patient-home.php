<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login-pacient.html");
    exit();
}

$prenume = $_SESSION['user'];
$cnp = $_SESSION['cnp'];

require_once 'config.php';

// Obținerea numărului de notificări necitite din tabelul notificari
$sql = "SELECT COUNT(*) as unread_count 
        FROM notificari n
        JOIN programari p ON n.programare_id = p.id
        WHERE p.pacient_CNP = ? AND n.destinatar = 'pacient' AND n.citit = 0";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('s', $cnp);
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
        WHERE cnp = ? AND destinatar = 'pacient' AND citit = 0";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('s', $cnp);
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

// Funcție pentru a obține un sfat medical aleatoriu
function getRandomSfat($conn) {
  $sql = "SELECT titlu, mesaj FROM sfaturi_medicale ORDER BY RAND() LIMIT 1";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
      die("Prepare statement failed: " . $conn->error);
  }
  if (!$stmt->execute()) {
      die("Execute statement failed: " . $stmt->error);
  }
  $result = $stmt->get_result();
  if (!$result) {
      die("Get result failed: " . $stmt->error);
  }
  $sfat = $result->fetch_assoc();
  $stmt->close();
  return $sfat;
}

// Verificarea și setarea sfatului în sesiune
if (!isset($_SESSION['sfat']) || $_SESSION['sfat']['date'] != date('Y-m-d')) {
  $sfat = getRandomSfat($conn);
  $_SESSION['sfat'] = [
      'titlu' => $sfat['titlu'],
      'mesaj' => $sfat['mesaj'],
      'date' => date('Y-m-d')
  ];
} else {
  $sfat = $_SESSION['sfat'];
}

$conn->close();

// Calcularea numărului total de notificări necitite
$total_unread = $unread_notifications + $unread_messages;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style/style.css">
  <title>Home</title>
</head>
<body>
  <div id="sidebar">
    <ul>
      <li><a href="calendar-pacient.php"><i class="fa fa-calendar fa-sm"></i> Programările Tale</a></li>
      <li><a href="past_medical_records.php"><i class="fa fa-notes-medical fa-sm"></i> Istoric Medical</a></li>
      <li><a href="webminarii.php"><i class="fa fa-graduation-cap fa-sm"></i>Webminarii</a></li>
    </ul>
  </div>

  <nav id="navbar" class="navsticky">
    <div class="container">
      <h1 class="logo"><a href="index.html">MedFM</a></h1>
      <ul>
        <li><a class="current" href="patient-home.php">Acasă</a></li>
        <li class="navbar-item">
          <a href="pacient-inbox.php">Inbox</a>
          <?php if ($total_unread > 0): ?>
            <span class="notification-badge"><?php echo $total_unread; ?></span>
          <?php endif; ?>
        </li>
        <li><a href="#">Bună, <?php echo htmlspecialchars($prenume); ?></a></li>
      </ul>
    </div>
  </nav>

  <div class="container container-home" style="margin-bottom: 69px; margin-top:110px;">
    <div class="container-sfat">
      <img src="img/sfat_1.png" alt="">
      <div class="sfat-container">
        <span class="sfat-titlu"><?php echo htmlspecialchars($sfat['titlu']); ?></span>
        <span class="sfat-mesaj"><?php echo htmlspecialchars($sfat['mesaj']); ?></span>
      </div>
    </div>
  </div>

  <a href="pacient-message.php" class="message-button">
    <i class="fa fa-comment"></i>
  </a>

  <footer id="secondary-footer">
    <div class="footer-elements">
        <div class="footer-item"><a href="about.php">Despre Noi</a></div>
        <div class="footer-item"><a href="contact.php">Contactează-ne</a></div>
        <div class="footer-item"><a href="#" id="gdpr-link">GDPR</a></div>
    </div>
    <p  style="font-weight: bold;" >MedFM &copy; 2024, Toate drepturile rezervate</p>
  </footer>

    <!-- Include GDPR Modal -->
    <?php include 'gdpr_modal.php'; ?>
  
</body>
</html>
