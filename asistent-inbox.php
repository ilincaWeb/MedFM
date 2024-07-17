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

// Obținerea medicului ID pentru asistentul autentificat
$stmt = $conn->prepare("SELECT id, nume, prenume, medic_id FROM asistenti WHERE CNP = ?");
$stmt->bind_param("s", $cnp);
$stmt->execute();
$stmt->bind_result($asistent_id, $asistent_nume, $asistent_prenume, $medic_id);
$stmt->fetch();
$stmt->close();

// Obținerea notificărilor pentru mesaje de la pacienți, excluzând mesajele trimise de asistent
$stmt = $conn->prepare("
    SELECT p.CNP as cnp, p.nume as nume, p.prenume as prenume, 
           MAX(m.data_trimiterii) as data_trimiterii, 
           COUNT(CASE WHEN m.destinatar = 'asistent' THEN m.id ELSE NULL END) as numar_mesaje, 
           MIN(CASE WHEN m.destinatar = 'asistent' THEN m.citit ELSE 1 END) as citit
    FROM mesaje m
    JOIN pacienti p ON m.cnp = p.CNP
    WHERE m.medic_id = ?
    GROUP BY p.CNP, p.nume, p.prenume
    ORDER BY MAX(m.data_trimiterii) DESC
");
$stmt->bind_param("i", $medic_id);
$stmt->execute();
$result = $stmt->get_result();
$notificari = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Obținerea numărului de notificări necitite din tabela notificari
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

// Obținerea numărului de mesaje necitite din tabela mesaje
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

// Funcție pentru formatarea datelor
function format_date($date_str) {
    $timestamp = strtotime($date_str);
    return date('l, d F Y', $timestamp);
}

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
    <h2 style="text-align: center;">NOTIFICĂRILE MELE</h2>
      <div class="button-container">
            <button class="active btn btn-mesaje" style="background-color: #618264;" onclick="location.href='#'">Mesaje <?php if ($unread_messages > 0) echo "($unread_messages)"; ?></button>
            <button class="btn btn-programari" onclick="location.href='asistent-inbox-prog.php'">Programari <?php if ($unread_notifications > 0) echo "($unread_notifications)"; ?></button>
        </div>
        <?php 
        if (empty($notificari)) {
            echo "<p>Nu aveți nicio notificare.</p>";
        } else {
            $current_date = '';
            foreach ($notificari as $notificare): 
                // Condiție suplimentară pentru a exclude notificările trimise de asistent
                $notificare_date = format_date($notificare['data_trimiterii']);
                if ($current_date !== $notificare_date): 
                    $current_date = $notificare_date; ?>
                    <div class="date-header"><?php echo $current_date; ?></div>
                <?php endif; ?>
                <a href="chat-template.php?cnp=<?php echo htmlspecialchars($notificare['cnp']); ?>" class="inbox-notification" style="<?php echo $notificare['citit'] ? '' : 'font-weight: bold;'; ?>">
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
    <p>MedFM &copy; 2023, Toate drepturile rezervate</p>
</footer>
</body>
</html>
