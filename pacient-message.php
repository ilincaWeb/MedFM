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

function format_date_in_romanian($date_string) {
    $days = [
        'Monday' => 'Luni',
        'Tuesday' => 'Marți',
        'Wednesday' => 'Miercuri',
        'Thursday' => 'Joi',
        'Friday' => 'Vineri',
        'Saturday' => 'Sâmbătă',
        'Sunday' => 'Duminică'
    ];

    $months = [
        'January' => 'Ianuarie',
        'February' => 'Februarie',
        'March' => 'Martie',
        'April' => 'Aprilie',
        'May' => 'Mai',
        'June' => 'Iunie',
        'July' => 'Iulie',
        'August' => 'August',
        'September' => 'Septembrie',
        'October' => 'Octombrie',
        'November' => 'Noiembrie',
        'December' => 'Decembrie'
    ];

    $timestamp = strtotime($date_string);
    $day_of_week = date('l', $timestamp);
    $day = date('d', $timestamp);
    $month = date('F', $timestamp);
    $year = date('Y', $timestamp);

    return $days[$day_of_week] . ' ' . $day . ', ' . $months[$month] . ' ' . $year;
}

// Obținerea numelui și prenumelui pacientului autentificat
$stmt = $conn->prepare("SELECT nume, prenume FROM pacienti WHERE CNP = ?");
$stmt->bind_param("s", $cnp);
$stmt->execute();
$stmt->bind_result($pacient_nume, $pacient_prenume);
$stmt->fetch();
$stmt->close();

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

// Marcarea mesajelor ca citite
$stmt = $conn->prepare("UPDATE mesaje SET citit = 1 WHERE cnp = ? AND nume = ? AND prenume = ? AND citit = 0");
$stmt->bind_param("sss", $cnp, $asistent_nume, $asistent_prenume);
$stmt->execute();
$stmt->close();

// Obținerea mesajelor anterioare
$stmt = $conn->prepare("SELECT * FROM mesaje WHERE cnp = ? ORDER BY data_trimiterii ASC");
$stmt->bind_param("s", $cnp);
$stmt->execute();
$result = $stmt->get_result();
$mesaje = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Gestionarea trimiterii mesajului
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mesaj'])) {
  $mesaj = htmlspecialchars($_POST['mesaj']);
  $destinatar = 'asistent';
  $stmt = $conn->prepare("INSERT INTO mesaje (medic_id, nume, prenume, cnp, mesaj, citit, destinatar) VALUES ((SELECT medic_id FROM pacienti WHERE CNP = ?), ?, ?, ?, ?, 0, ?)");
  if (!$stmt) {
      die('Prepare failed: ' . $conn->error);
  }
  $stmt->bind_param("ssssss", $cnp, $pacient_nume, $pacient_prenume, $cnp, $mesaj, $destinatar);
  if (!$stmt->execute()) {
      die('Execute failed: ' . $stmt->error);
  }
  $stmt->close();
  header("Location: pacient-message.php");
  exit();
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
  <link rel="stylesheet" href="style/style.css">
  <title>Chat cu Asistent</title>
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

  <div class="container container-home">
    <div class="chat-container">
      <div class="chat-header">
        <h2>Chat cu <?php echo htmlspecialchars($asistent_nume . ' ' . $asistent_prenume); ?></h2>
      </div>
      <div class="message-container chat-messages">
        <?php
        $current_date = '';
        foreach ($mesaje as $mesaj) {
            $date = format_date_in_romanian($mesaj['data_trimiterii']);
            if ($current_date !== $date) {
                echo "<div class='chat-date'>{$date}</div>";
                $current_date = $date;
            }
            $is_asistent = ($mesaj['nume'] === $asistent_nume && $mesaj['prenume'] === $asistent_prenume);
            $color = $is_asistent ? 'green' : 'red';
            $nume_prenume = htmlspecialchars($mesaj['nume']) . " " . htmlspecialchars($mesaj['prenume']);
            echo "<div class='chat-message'><span style='color: {$color};'>{$nume_prenume}: </span>{$mesaj['mesaj']}</div>";
        }
        ?>
      </div>
      <form action="pacient-message.php" method="post" class="chat-form input-container">
        
        <input type="text" name="mesaj" required placeholder="Scrie un mesaj...">
        <button type="submit"><i class="fa fa-paper-plane"></i></button>
      </form>
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
