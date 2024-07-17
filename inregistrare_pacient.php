<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login_doctor.html");
    exit();
}
$prenume = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style/inregistrare.css">
  <title>Înregistrare Pacient</title>
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

<div class="form-container container-home">
  <h2>Înregistrare Pacient</h2>
  <?php if (isset($_SESSION['pacient_message'])): ?>
      <div class="message <?php echo $_SESSION['pacient_message_type']; ?>">
          <?php
          echo $_SESSION['pacient_message'];
          unset($_SESSION['pacient_message']);
          unset($_SESSION['pacient_message_type']);
          ?>
      </div>
  <?php endif; ?>
  <form action="submit_pacient.php" method="POST">
    <div class="form-group">
      <label for="nume">Nume:</label>
      <input type="text" id="nume" name="nume" required>
    </div>
    <div class="form-group">
      <label for="prenume">Prenume:</label>
      <input type="text" id="prenume" name="prenume" required>
    </div>
    <div class="form-group">
      <label for="sex">Sex:</label>
      <select id="sex" name="sex" required>
        <option value="M">Masculin</option>
        <option value="F">Feminin</option>
        <option value="F">Prefer să nu spun</option>
      </select>
    </div>
    <div class="form-group">
      <label for="data_nasterii">Data Nașterii:</label>
      <input type="date" id="data_nasterii" name="data_nasterii" required>
    </div>
    <div class="form-group">
      <label for="CNP">CNP:</label>
      <input type="text" id="CNP" name="CNP" required>
    </div>
    <div class="form-group">
      <label for="varsta">Vârstă:</label>
      <input type="number" id="varsta" name="varsta" required>
    </div>
    <div class="form-group">
      <label for="greutate">Greutate (kg):</label>
      <input type="number" id="greutate" name="greutate" step="0.1">
    </div>
    <div class="form-group">
      <label for="inaltime">Înălțime (cm):</label>
      <input type="number" id="inaltime" name="inaltime" step="0.1">
    </div>
    <div class="form-group">
      <label for="stare_civila">Stare Civilă:</label>
      <input type="text" id="stare_civila" name="stare_civila">
    </div>
    <div class="form-group">
      <label for="ocupatie">Ocupație:</label>
      <input type="text" id="ocupatie" name="ocupatie">
    </div>
    <div class="form-group">
      <label for="reactii_adverse">Reacții Adverse:</label>
      <textarea id="reactii_adverse" name="reactii_adverse"></textarea>
    </div>
    <button type="submit">Înregistrează</button>
  </form>
</div>



</body>
</html>
