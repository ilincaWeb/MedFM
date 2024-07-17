<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['medic_id'])) {
    header("Location: login_doctor.html");
    exit();
}

include 'config.php';

$medic_id = $_SESSION['medic_id'];

$sql_concedii = "SELECT c.id, c.dataEliberarii, p.prenume AS pacient_prenume, p.nume AS pacient_nume 
                FROM concedii_medicale c 
                JOIN pacienti p ON c.cnp = p.CNP 
                WHERE p.medic_id = ?";
$stmt_concedii = $conn->prepare($sql_concedii);
$stmt_concedii->bind_param('i', $medic_id);
$stmt_concedii->execute();
$result_concedii = $stmt_concedii->get_result();

$sql_trimiteri = "SELECT t.id, t.data_trimiterii, p.prenume AS pacient_prenume, p.nume AS pacient_nume 
                 FROM trimiteri t 
                 JOIN pacienti p ON t.cid_cnp_ce_pass = p.CNP 
                 WHERE p.medic_id = ?";
$stmt_trimiteri = $conn->prepare($sql_trimiteri);
$stmt_trimiteri->bind_param('i', $medic_id);
$stmt_trimiteri->execute();
$result_trimiteri = $stmt_trimiteri->get_result();

$sql_retete = "SELECT r.id, r.data_prescriere, p.prenume AS pacient_prenume, p.nume AS pacient_nume 
              FROM retete_compensate r 
              JOIN pacienti p ON r.cid_ce_pass = p.CNP 
              WHERE p.medic_id = ?";
$stmt_retete = $conn->prepare($sql_retete);
$stmt_retete->bind_param('i', $medic_id);
$stmt_retete->execute();
$result_retete = $stmt_retete->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
  <title>Existing Medical Records</title>
  <link rel="stylesheet" href="style/inbox.css">
  
</head>
<body>
<div id="sidebar">
    <ul>
      <li><a href="calendar-doctor.php"><i class="fa fa-calendar fa-sm"></i> Programările Mele</a></li>
      <li><a href="medical-records.php"><i class="fa fa-notes-medical fa-sm"></i> Istoric Medical</a></li>
    </ul>
  </div>

  <nav id="navbar" class="navsticky">
    <div class="container">
      <h1 class="logo"><a href="index.html">MedFM</a></h1>
      <ul>
        <li><a href="contact.html">Contactează-ne</a></li>
        <li><a href="profile.html">Bună, <?php echo htmlspecialchars($_SESSION['user']); ?></a></li>
      </ul>
    </div>
  </nav>

  <div class="container container-home past-medical">
    <h2>Documente Medicale Existente</h2>
    <input type="text" id="searchBar" class="search-bar" placeholder="Caută după...">
    <p id="noResults" class="no-results">Niciun rezultat găsit.</p>
    <ul id="recordsList">
      <?php
      while ($row_concedii = $result_concedii->fetch_assoc()) {
        echo "<li><a href='view_form.php?id=" . $row_concedii['id'] . "&type=concediu'>Concediu medical pentru " . htmlspecialchars($row_concedii['pacient_nume']) . " " . htmlspecialchars($row_concedii['pacient_prenume']) . " din data " . $row_concedii['dataEliberarii'] . "</a></li>";
      }
      while ($row_trimiteri = $result_trimiteri->fetch_assoc()) {
        echo "<li><a href='view_form.php?id=" . $row_trimiteri['id'] . "&type=trimitere'>Trimitere pentru " . htmlspecialchars($row_trimiteri['pacient_nume']) . " " . htmlspecialchars($row_trimiteri['pacient_prenume']) . " din data " . $row_trimiteri['data_trimiterii'] . "</a></li>";
      }
      while ($row_retete = $result_retete->fetch_assoc()) {
        echo "<li><a href='view_form.php?id=" . $row_retete['id'] . "&type=reteta'>Rețetă compensată pentru " . htmlspecialchars($row_retete['pacient_nume']) . " " . htmlspecialchars($row_retete['pacient_prenume']) . " din data " . $row_retete['data_prescriere'] . "</a></li>";
      }
      ?>
    </ul>
  </div>

  <footer id="secondary-footer" class="small">
  <p>MedFM &copy; 2024, Toate drepturile rezervate</p>
  </footer>

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
<?php
$stmt_concedii->close();
$stmt_trimiteri->close();
$stmt_retete->close();
$conn->close();
?>
