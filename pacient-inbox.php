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

require_once 'config.php';
require_once 'functions.php';

// Obținerea asistentului asociat cu medicul pacientului
$stmt = $conn->prepare("
    SELECT a.nume, a.prenume 
    FROM asistenti a
    JOIN pacienti p ON a.medic_id = p.medic_id
    WHERE p.CNP = ?
");
$stmt->bind_param("s", $cnp);
$stmt->execute();
$stmt->bind_result($asistent_nume, $asistent_prenume);
$stmt->fetch();
$stmt->close();

// Obținerea notificărilor pentru mesaje noi și citite de la asistent
$stmt = $conn->prepare("
    SELECT m.cnp, m.prenume, m.nume, MAX(m.data_trimiterii) as data_trimiterii, COUNT(*) as numar_mesaje, m.citit
    FROM mesaje m
    WHERE m.cnp = ? AND m.destinatar = 'pacient'
    GROUP BY m.cnp, m.prenume, m.nume, m.citit
    ORDER BY MAX(m.data_trimiterii) DESC
");
$stmt->bind_param("s", $cnp);
$stmt->execute();
$result = $stmt->get_result();
$notificari = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// Funcție pentru formatarea datelor
function format_date($date_str) {
    $timestamp = strtotime($date_str);
    return date('l, d F Y', $timestamp);
}

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
  <link rel="stylesheet" href="style/inbox.css">
  <title>Inbox Pacient</title>
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
  </style>
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
        <li><a href="patient-home.php">Acasă</a></li>
        <li class="navbar-item">
          <a class="current"  href="pacient-inbox.php">Inbox</a>
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
            <button class="active btn btn-mesaje" style="background-color: #618264;" onclick="location.href='#'">Mesaje <?php if ($unread_messages > 0) echo "($unread_messages)"; ?></button>
            <button class="btn btn-programari" onclick="location.href='pacient-inbox-prog.php'">Programari <?php if ($unread_notifications > 0) echo "($unread_notifications)"; ?></button>
        </div>
      </div>
      <?php 
      if (empty($notificari)) {
        echo "<p>Nu aveți nicio notificare.</p>";
      } else {
        $current_date = '';
        foreach ($notificari as $notificare): 
          $notificare_date = format_date($notificare['data_trimiterii']);
          if ($current_date !== $notificare_date): 
            $current_date = $notificare_date; ?>
            <div class="date-header"><?php echo $current_date; ?></div>
          <?php endif; ?>
          <a href="pacient-message.php?cnp=<?php echo htmlspecialchars($notificare['cnp']); ?>" class="inbox-notification" style="<?php echo $notificare['citit'] ? '' : 'font-weight: bold;'; ?>">
            Mesaj de la <?php echo htmlspecialchars($notificare['nume']) . ' ' . htmlspecialchars($notificare['prenume']); ?>
            (<?php echo $notificare['numar_mesaje']; ?>)
            <?php if (!$notificare['citit']): ?>
              <span class="new-message">Mesaj nou</span>
            <?php endif; ?>
          </a>
        <?php endforeach;
      } ?>
    </div>
  </div>
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