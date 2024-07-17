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

// Calcularea numărului total de notificări necitite
$total_unread = $unread_notifications + $unread_messages;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style/about.css">
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

  <div class="container container-home contact">
  <h2>Contactează-ne</h2>
  <?php if (isset($_SESSION['contact_message'])): ?>
      <div class="message <?php echo $_SESSION['contact_message_type']; ?>">
          <?php
          echo $_SESSION['contact_message'];
          unset($_SESSION['contact_message']);
          unset($_SESSION['contact_message_type']);
          ?>
      </div>
  <?php endif; ?>
  <form action="submit_contact.php" method="POST" class="contact-form">
    <div class="form-group">
      <label for="name">Nume:</label>
      <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="subject">Subiect:</label>
      <input type="text" id="subject" name="subject" required>
    </div>
    <div class="form-group">
      <label for="message">Mesaj:</label>
      <textarea id="message" name="message" rows="5" required></textarea>
    </div>
    <button type="submit" class="submit-button">Trimite</button>
  </form>
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