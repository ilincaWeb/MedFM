<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login_doctor.html");
    exit();
}
$prenume = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stergere Pacient</title>
    <link rel="stylesheet" href="style/stergere.css">
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
        <li><a href="#">Bună, <?php echo htmlspecialchars($_SESSION['user']); ?></a></li>
      </ul>
    </div>
  </nav>

  <div class="container-centrat">
    <h2>Stergere Pacient</h2>
    <div class="form-container">
      <?php if (isset($_SESSION['message'])): ?>
          <div class="message <?php echo $_SESSION['message_type']; ?>">
              <?php
              echo $_SESSION['message'];
              unset($_SESSION['message']);
              unset($_SESSION['message_type']);
              ?>
          </div>
      <?php endif; ?>
      <form action="submit_stergere_pacient.php" method="POST">
        <div class="form-group">
          <label for="cnp">CNP Pacient:</label>
          <input type="text" id="cnp" name="cnp" required>
        </div>
        <button type="submit">Sterge Pacient</button>
      </form>
    </div>
  </div>
</body>
</html>
