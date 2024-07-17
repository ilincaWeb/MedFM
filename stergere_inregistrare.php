<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login_doctor.html");
    exit();
}
$prenume = $_SESSION['user'];  // Asumăm că $_SESSION['user'] conține CNP-ul medicului
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilet Trimitere pentru Investigații Paraclinice</title>
    <link rel="stylesheet" href="style/stergere_inregistrare.css">
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

  <div class="container-sterg">
    <div class="button-container">
        <a class="btn btn-dark" href="stergere_pacient.php">Stergere</a>
        <a class="btn btn-dark" href="inregistrare_pacient.php">Inregistrare</a>
    </div>
</div>

</body>
</html>