<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login-pacient.html");
    exit();
}
$prenume = $_SESSION['user'];
$cnp = $_SESSION['cnp'];
include 'config.php';


$cnp_pacient = $_SESSION['cnp'];

$sql_concedii = "SELECT id, dataEliberarii FROM concedii_medicale WHERE cnp = ?";
$stmt_concedii = $conn->prepare($sql_concedii);
$stmt_concedii->bind_param('s', $cnp_pacient);
$stmt_concedii->execute();
$result_concedii = $stmt_concedii->get_result();

$sql_trimiteri = "SELECT id, data_trimiterii FROM trimiteri WHERE cid_cnp_ce_pass = ?";
$stmt_trimiteri = $conn->prepare($sql_trimiteri);
$stmt_trimiteri->bind_param('s', $cnp_pacient);
$stmt_trimiteri->execute();
$result_trimiteri = $stmt_trimiteri->get_result();

$sql_retete = "SELECT id, data_prescriere FROM retete_compensate WHERE cid_ce_pass = ?";
$stmt_retete = $conn->prepare($sql_retete);
$stmt_retete->bind_param('s', $cnp_pacient);
$stmt_retete->execute();
$result_retete = $stmt_retete->get_result();


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
  <title>Past Medical Records</title>
  <link rel="stylesheet" href="style/inbox.css">
</head>
<body>
  <div id="sidebar">
    <ul>
      <li><a href="calendar-pacient.php"><i class="fa fa-calendar fa-sm"></i> Programările Tale</a></li>
      <li><a href="#"><i class="fa fa-notes-medical fa-sm"></i> Istoric Medical</a></li>
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

  <div class="container container-home past-medical">
    <input type="text" id="searchBar" class="search-bar" placeholder="Căutare după dată...">
    <p id="noResults" class="no-results">Niciun rezultat găsit.</p>
    <ul id="recordsList">
      <?php
      while ($row_concedii = $result_concedii->fetch_assoc()) {
        echo "<li><a href='view_form.php?id=" . $row_concedii['id'] . "&type=concediu'>Concediu medical din data " . $row_concedii['dataEliberarii'] . "</a></li>";
      }
      while ($row_trimiteri = $result_trimiteri->fetch_assoc()) {
        echo "<li><a href='view_form.php?id=" . $row_trimiteri['id'] . "&type=trimitere'>Trimitere din data " . $row_trimiteri['data_trimiterii'] . "</a></li>";
      }
      while ($row_retete = $result_retete->fetch_assoc()) {
        echo "<li><a href='view_form.php?id=" . $row_retete['id'] . "&type=reteta'>Rețetă compensată din data " . $row_retete['data_prescriere'] . "</a></li>";
      }
      ?>
    </ul>
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

  <script>
    document.getElementById('searchBar').addEventListener('keyup', function() {
      var input = this.value.toLowerCase();
      var records = document.getElementById('recordsList').getElementsByTagName('li');
      var noResults = document.getElementById('noResults');
      var found = false;

      for (var i = 0; i < records.length; i++) {
        var record = records[i].getElementsByTagName('a')[0];
        var textValue = record.textContent || record.innerText;

        if (textValue.toLowerCase().indexOf(input) > -1) {
          records[i].style.display = '';
          found = true;
        } else {
          records[i].style.display = 'none';
        }
      }

      noResults.style.display = found ? 'none' : 'block';
    });
  </script>

</body>
</html>
