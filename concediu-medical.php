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
  <link rel="stylesheet" href="style/concediu.css">
  <title>Certificat de Concediu Medical</title>
  <script defer src="script.js"></script>
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
  <form id="medicalForm" action="submit_concediu.php" method="POST">
    <div class="certificate-container container-home" id="certificate">
      <header>
        <div class="header-section">
          <div class="header-left">
            <h3>CASA NAȚIONALĂ DE ASIGURĂRI DE SĂNĂTATE</h3>
            <h4>CASA DE ASIGURĂRI DE SĂNĂTATE</h4>
          </div>
          <div class="header-right">
            <div class="serie-numar">
              <label for="seria">Seria:</label>
              <input type="text" id="seria" name="seria">
            </div>
            <div class="serie-numar">
              <label for="nr">Numărul:</label>
              <input type="text" id="nr" name="nr">
            </div>
          </div>
        </div>
        <h2>Certificat de concediu medical</h2>
      </header>
      <section class="info-section">
        <div class="row">
          <label for="nume">Numele și prenumele asiguratului:</label>
          <input type="text" id="nume" name="nume">
        </div>
        <div class="row">
          <label for="cnp">Cod numeric personal:</label>
          <input type="text" id="cnp" name="cnp">
        </div>
        <div class="row">
          <label for="cnpCopil">Cod numeric personal al copilului bolnav:</label>
          <input type="text" id="cnpCopil" name="cnpCopil">
        </div>
        <div class="row">
          <label for="localitate">Adresa:  Localitatea:</label>
          <input type="text" id="localitate" name="localitate">
        </div>
        <div class="row">
          <label for="strada">Strada:</label>
          <input type="text" id="strada" name="strada">
        </div>
        <div class="row">
          <label for="nrS">Nr.:</label>
          <input type="text" id="nrS" name="nrS">
        </div>
        <div class="row">
          <label for="bloc">Bl.:</label>
          <input type="text" id="bloc" name="bloc">
        </div>
        <div class="row">
          <label for="scara">Scara:</label>
          <input type="text" id="scara" name="scara">
        </div>
        <div class="row">
          <label for="etaj">Etaj:</label>
          <input type="text" id="etaj" name="etaj">
        </div>
        <div class="row">
          <label for="apart">Apart.:</label>
          <input type="text" id="apart" name="apart">
        </div>
        <div class="row">
          <label for="judet">Judet/Sector:</label>
          <input type="text" id="judet" name="judet">
        </div>
        <div class="row">
          <label for="unitatea">Unitatea:</label>
          <input type="text" id="unitatea" name="unitatea">
        </div>
        <div class="row">
          <label for="sectia">Secția:</label>
          <input type="text" id="sectia" name="sectia">
        </div>
        <div class="row">
          <label for="diagnostic">Diagnostic:</label>
          <input type="text" id="diagnostic" name="diagnostic">
        </div>
        <div class="row">
          <label for="cod_diagnostic">Cod diagnostic:</label>
          <input type="text" id="cod_diagnostic" name="cod_diagnostic">
        </div>
        <div class="row">
          <label for="aviz">Aviz:</label>
          <input type="text" id="aviz" name="aviz">
        </div>
        <div class="row">
          <label for="deLa">De la zi/luna/an:</label>
          <input type="date" id="deLa" name="deLa">
        </div>
        <div class="row">
          <label for="nrZile">Numar zile:</label>
          <input type="text" id="nrZile" name="nrZile">
        </div>
        <div class="row">
          <label for="panaLa">Pana la zi/luna/an:</label>
          <input type="date" id="panaLa" name="panaLa">
        </div>
        <div class="row">
          <label for="cod_urgenta">Cod urgență:</label>
          <input type="text" id="cod_urgenta" name="cod_urgenta">
        </div>
        <div class="row">
          <label for="medic">Numele și parafa medicului:</label>
          <input type="text" id="medic" name="medic">
        </div>
      </section>
      <footer>
        <div class="footer-left">
          <p>Data eliberării:</p>
          <input type="date" id="dataEliberarii" name="dataEliberarii">
        </div>
        <div class="footer-right">
          <p>Semnătura și parafa medicului</p>
        </div>
      </footer>
    </div>
    <button onclick="printCertificate()">Print</button>
    <button type="submit">Submit</button>
  </form>

  <!-- Modal -->
   <div id="myModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p id="modalMessage"></p>
    </div>
  </div>

  <script>
    document.getElementById('medicalForm').addEventListener('submit', function(event) {
      event.preventDefault(); // Previne trimiterea formularului și reîncărcarea paginii
      const formData = new FormData(this); // Preia datele formularului

      // Trimite datele către server folosind AJAX
      fetch('submit_concediu.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        // Afișează mesajul de succes în modal
        document.getElementById('modalMessage').innerText = data;
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
      })
      .catch(error => {
        console.error('Eroare:', error);
        alert('A apărut o eroare la înregistrarea formularului.'); // Afișează pop-up de eroare
      });
    });

    // Gestionarea închiderii modalului
    var modal = document.getElementById("myModal");
    var span = document.getElementsByClassName("close")[0];
    span.onclick = function() {
      modal.style.display = "none";
      document.getElementById('modalMessage').innerText = ''; // Șterge mesajul din modal
    }
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
        document.getElementById('modalMessage').innerText = ''; // Șterge mesajul din modal
      }
    }
  </script>
</body>
</html>