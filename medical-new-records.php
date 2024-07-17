<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login_doctor.html");
    exit();
}
$prenume = $_SESSION['user'];
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style/style.css">
  <title>Calendar</title>
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

  <div class="crads-grid-container">
    <a href="concediu-medical.php"><button class="card-new">CONCEDIU MEDICAL</button></a>
    <a href="trimitere-investigatii.php"> <button class="card-new">TRIMITERE INVESTIGATII</button></a>
    <a href="reteta_compensata.php"><button class="card-new">RETETA COMPENSATA</button></a>
    <a href="stergere_inregistrare.php"><button class="card-new">INREGISTRARE/STERGERE <br>PACIENT NOU</button></a>
</div>



</body>
</html>