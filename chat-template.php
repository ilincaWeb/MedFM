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

$pacient_cnp = $_GET['cnp'];

// Obținerea medicului ID și detaliilor pentru asistentul autentificat
$stmt = $conn->prepare("SELECT id, nume, prenume FROM asistenti WHERE CNP = ?");
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("s", $cnp);
if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}
$stmt->bind_result($asistent_id, $asistent_nume, $asistent_prenume);
$stmt->fetch();
$stmt->close();

// Obținerea numelui și prenumelui pacientului
$stmt = $conn->prepare("SELECT nume, prenume FROM pacienti WHERE CNP = ?");
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("s", $pacient_cnp);
if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}
$stmt->bind_result($pacient_nume, $pacient_prenume);
$stmt->fetch();
$stmt->close();

// Marcarea mesajelor ca citite
$stmt = $conn->prepare("UPDATE mesaje SET citit = 1 WHERE cnp = ? AND medic_id = ? AND nume != ? AND prenume != ?");
$stmt->bind_param("siss", $pacient_cnp, $asistent_id, $asistent_nume, $asistent_prenume);
$stmt->execute();
$stmt->close();

// Obținerea mesajelor anterioare
$stmt = $conn->prepare("SELECT * FROM mesaje WHERE cnp = ? AND medic_id = ? ORDER BY data_trimiterii ASC");
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("si", $pacient_cnp, $asistent_id);
if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}
$result = $stmt->get_result();
$mesaje = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Gestionarea trimiterii mesajului
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mesaj'])) {
    $mesaj = htmlspecialchars($_POST['mesaj']);
    $destinatar = 'pacient';
    $stmt = $conn->prepare("INSERT INTO mesaje (medic_id, nume, prenume, cnp, mesaj, citit, destinatar) VALUES (?, ?, ?, ?, ?, 0, ?)");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("isssss", $asistent_id, $asistent_nume, $asistent_prenume, $pacient_cnp, $mesaj, $destinatar);
    if (!$stmt->execute()) {
        die('Execute failed: ' . $stmt->error);
    }
    $stmt->close();
    header("Location: chat-template.php?cnp=" . htmlspecialchars($pacient_cnp));
    exit();
}

// Obținerea numărului de notificări necitite
$sql = "SELECT COUNT(*) as unread_count 
        FROM notificari n
        JOIN programari p ON n.programare_id = p.id
        WHERE p.medic_id = ? AND n.destinatar = 'asistent' AND n.citit = 0";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('i', $asistent_id);
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
$stmt->bind_param('i', $asistent_id);
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

$total_unread = $unread_notifications + $unread_messages;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style/style.css">
    <title>Chat Asistent</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var chatMessages = document.querySelector(".chat-messages");
            chatMessages.scrollTop = chatMessages.scrollHeight;
            var chatForm = document.querySelector(".chat-form");
        chatForm.addEventListener("submit", function() {
            setTimeout(function() {
                chatMessages.scrollTop = chatMessages.scrollHeight;
                document.querySelector("input[name='mesaj']").focus();
            }, 100);
        });
    });
</script>
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
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat cu <?php echo htmlspecialchars($pacient_nume . ' ' . $pacient_prenume); ?></h2>
        </div>
        <div class="chat-messages">
            <?php
            $current_date = '';
            foreach ($mesaje as $mesaj) {
                $date = date('l, d F Y', strtotime($mesaj['data_trimiterii']));
                if ($current_date !== $date) {
                    echo "<div class='chat-date'>{$date}</div>";
                    $current_date = $date;
                }
                // Determinăm dacă mesajul a fost trimis de asistent sau de pacient
                $is_asistent = ($mesaj['nume'] === $asistent_nume && $mesaj['prenume'] === $asistent_prenume);
                $color = $is_asistent ? 'red' : 'green';
                $nume_prenume = $is_asistent ? htmlspecialchars($asistent_nume . ' ' . $asistent_prenume) : htmlspecialchars($pacient_nume . ' ' . $pacient_prenume);
                echo "<div class='chat-message'><span style='color: {$color};'>{$nume_prenume}: </span>{$mesaj['mesaj']}</div>";
            }
            ?>
        </div>
        <form action="chat-template.php?cnp=<?php echo htmlspecialchars($pacient_cnp); ?>" method="post" class="chat-form">
            <input type="text" name="mesaj" required placeholder="Scrie un mesaj...">
            <button type="submit"><i class="fa fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<footer id="secondary-footer">
    <p>MedFM &copy; 2023, Toate drepturile rezervate</p>
</footer>
</body>
</html>