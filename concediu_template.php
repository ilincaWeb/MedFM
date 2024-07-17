<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificat de concediu medical</title>
  <link rel="stylesheet" href="style/style.css">
</head>
<body>
  <div id="sidebar">
    <ul>
      <li><a href="#"><i class="fa fa-user fa-sm"></i> Profile</a></li>
      <li><a href="calendar-pacient.html"><i class="fa fa-calendar fa-sm"></i> Your Appointments</a></li>
      <li><a href="past_medical_records.php"><i class="fa fa-notes-medical fa-sm"></i> Past Records</a></li>
      <li><a href="#">Item 4</a></li>
    </ul>
  </div>

  <nav id="navbar" class="navsticky">
    <div class="container">
      <h1 class="logo"><a href="index.html">MedFM</a></h1>
      <ul>
        <li><a class="current" href="index.html">Home</a></li>
        <li><a href="profile.html">Bună, <?php echo htmlspecialchars($_SESSION['user']); ?></a></li>
      </ul>
    </div>
  </nav>

  <div class="container container-home">
    <h2>Certificat de concediu medical</h2>
    <div class="certificate-container" id="certificate">
      <header>
        <div class="header-section">
          <div class="header-left">
            <h3>CASA NAȚIONALĂ DE ASIGURĂRI DE SĂNĂTATE</h3>
            <h4>CASA DE ASIGURĂRI DE SĂNĂTATE</h4>
          </div>
          <div class="header-right">
            <div class="serie-numar">
              <label for="seria">Seria:</label>
              <input type="text" id="seria" name="seria" value="<?php echo htmlspecialchars($form_data['seria']); ?>" readonly>
            </div>
            <div class="serie-numar">
              <label for="nr">Numărul:</label>
              <input type="text" id="nr" name="nr" value="<?php echo htmlspecialchars($form_data['nr']); ?>" readonly>
            </div>
          </div>
        </div>
        <h2>Certificat de concediu medical</h2>
      </header>
      <section class="info-section">
        <div class="row">
          <label for="nume">Numele și prenumele asiguratului:</label>
          <input type="text" id="nume" name="nume" value="<?php echo htmlspecialchars($form_data['nume']); ?>" readonly>
        </div>
        <div class="row">
          <label for="cnp">Cod numeric personal:</label>
          <input type="text" id="cnp" name="cnp" value="<?php echo htmlspecialchars($form_data['cnp']); ?>" readonly>
        </div>
        <div class="row">
          <label for="cnpCopil">Cod numeric personal al copilului bolnav:</label>
          <input type="text" id="cnpCopil" name="cnpCopil" value="<?php echo htmlspecialchars($form_data['cnpCopil']); ?>" readonly>
        </div>
        <div class="row">
          <label for="localitate">Adresa:  Localitatea:</label>
          <input type="text" id="localitate" name="localitate" value="<?php echo htmlspecialchars($form_data['localitate']); ?>" readonly>
        </div>
        <div class="row">
          <label for="strada">Strada:</label>
          <input type="text" id="strada" name="strada" value="<?php echo htmlspecialchars($form_data['strada']); ?>" readonly>
        </div>
        <div class="row">
          <label for="nrS">Nr.:</label>
          <input type="text" id="nrS" name="nrS" value="<?php echo htmlspecialchars($form_data['nrS']); ?>" readonly>
        </div>
        <div class="row">
          <label for="bloc">Bl.:</label>
          <input type="text" id="bloc" name="bloc" value="<?php echo htmlspecialchars($form_data['bloc']); ?>" readonly>
        </div>
        <div class="row">
          <label for="scara">Scara:</label>
          <input type="text" id="scara" name="scara" value="<?php echo htmlspecialchars($form_data['scara']); ?>" readonly>
        </div>
        <div class="row">
          <label for="etaj">Etaj:</label>
          <input type="text" id="etaj" name="etaj" value="<?php echo htmlspecialchars($form_data['etaj']); ?>" readonly>
        </div>
        <div class="row">
          <label for="apart">Apart.:</label>
          <input type="text" id="apart" name="apart" value="<?php echo htmlspecialchars($form_data['apart']); ?>" readonly>
        </div>
        <div class="row">
          <label for="judet">Judet/Sector:</label>
          <input type="text" id="judet" name="judet" value="<?php echo htmlspecialchars($form_data['judet']); ?>" readonly>
        </div>
        <div class="row">
          <label for="unitatea">Unitatea:</label>
          <input type="text" id="unitatea" name="unitatea" value="<?php echo htmlspecialchars($form_data['unitatea']); ?>" readonly>
        </div>
        <div class="row">
          <label for="sectia">Secția:</label>
          <input type="text" id="sectia" name="sectia" value="<?php echo htmlspecialchars($form_data['sectia']); ?>" readonly>
        </div>
        <div class="row">
          <label for="diagnostic">Diagnostic:</label>
          <input type="text" id="diagnostic" name="diagnostic" value="<?php echo htmlspecialchars($form_data['diagnostic']); ?>" readonly>
        </div>
        <div class="row">
          <label for="cod_diagnostic">Cod diagnostic:</label>
          <input type="text" id="cod_diagnostic" name="cod_diagnostic" value="<?php echo htmlspecialchars($form_data['cod_diagnostic']); ?>" readonly>
        </div>
        <div class="row">
          <label for="aviz">Aviz:</label>
          <input type="text" id="aviz" name="aviz" value="<?php echo htmlspecialchars($form_data['aviz']); ?>" readonly>
        </div>
        <div class="row">
          <label for="deLa">De la zi/luna/an:</label>
          <input type="text" id="deLa" name="deLa" value="<?php echo htmlspecialchars($form_data['deLa']); ?>" readonly>
        </div>
        <div class="row">
          <label for="nrZile">Numar zile:</label>
          <input type="text" id="nrZile" name="nrZile" value="<?php echo htmlspecialchars($form_data['nrZile']); ?>" readonly>
        </div>
        <div class="row">
          <label for="panaLa">Pana la zi/luna/an:</label>
          <input type="text" id="panaLa" name="panaLa" value="<?php echo htmlspecialchars($form_data['panaLa']); ?>" readonly>
        </div>
        <div class="row">
          <label for="cod_urgenta">Cod urgență:</label>
          <input type="text" id="cod_urgenta" name="cod_urgenta" value="<?php echo htmlspecialchars($form_data['cod_urgenta']); ?>" readonly>
        </div>
        <div class="row">
          <label for="medic">Numele și parafa medicului:</label>
          <input type="text" id="medic" name="medic" value="<?php echo htmlspecialchars($form_data['medic']); ?>" readonly>
        </div>
      </section>
      <footer>
        <div class="footer-left">
          <p>Data eliberării:</p>
          <input type="text" id="dataEliberarii" name="dataEliberarii" value="<?php echo htmlspecialchars($form_data['dataEliberarii']); ?>" readonly>
        </div>
        <div class="footer-right">
          <p>Semnătura și parafa medicului</p>
        </div>
      </footer>
    </div>
  </div>

  <footer id="secondary-footer">
    <p>MedFM &copy; 2023, All Rights Reserved</p>
  </footer>
</body>
</html>
