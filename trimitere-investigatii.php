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
    <title>Bilet Trimitere pentru Investigații Paraclinice</title>
    <link rel="stylesheet" href="style/trimitere.css">
    <style>
        /* Style pentru modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
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
  <div class="certificate-container container-home">
    <header>
      <h1>Bilet trimitere pentru investigații paraclinice decontate de CAS</h1>
      <form id="trimitereForm" action="submit_investigatii.php" method="post">
        <div class="serie-nr">
          <label for="serie">Serie:</label>
          <input type="text" id="serie" name="serie">
          <label for="numar">Nr.:</label>
          <input type="text" id="numar" name="numar">
        </div>
        <div class="grid-container">
          <div class="grid-row">
            <div class="grid-item grid-item-1">
              <div class="sub-item">
                <label for="unitate_medicala">1. Unitate medicala</label>
                <input type="text" id="unitate_medicala" name="unitate_medicala">
              </div>
              <div class="sub-item">
                <label for="cui">CUI</label>
                <input type="text" id="cui" name="cui">
              </div>
              <div class="sub-item">
                <label for="sediu">Sediu (localitate, str., nr.)</label>
                <input type="text" id="sediu" name="sediu">
              </div>
              <div class="sub-item">
                <label for="judetul">Judetul</label>
                <input type="text" id="judetul" name="judetul">
              </div>
              <div class="sub-item">
                <label for="casa_asigurari">Casa de asigurari:</label>
                <input type="text" id="casa_asigurari" name="casa_asigurari">
              </div>
              <div class="sub-item">
                <label for="nr_contract">Nr. contract/conventie</label>
                <input type="text" id="nr_contract" name="nr_contract">
              </div>
            </div>
            <div class="grid-item grid-item-2">
              <div class="sub-item">
                <input type="checkbox" id="mf" name="mf">
                <label for="mf">MF</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="amb_spec" name="amb_spec">
                <label for="amb_spec">Amb. Spec.</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="unitate_sanitara" name="unitate_sanitara">
                <label for="unitate_sanitara">Unitate sanitara cu paturi</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="altele" name="altele">
                <label for="altele">Altele</label>
              </div>
            </div>
            <div class="grid-item grid-item-3">
              <div class="sub-item">
                <p>Nivel de prioritate</p>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="prioritate_urgenta" name="prioritate_urgenta">
                <label for="prioritate_urgenta">Urgenta</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="prioritate_curente" name="prioritate_curente">
                <label for="prioritate_curente">Curente</label>
              </div>
            </div>
          </div>
          <div class="grid-row">
            <div class="grid-item grid-item-1">
              <div class="sub-item">
                <p>2. Date de identificare asigurat</p>
              </div>
              <div class="sub-item">
                <label for="asigurat_la_cas">Asigurat la CAS:</label>
                <input type="text" id="asigurat_la_cas" name="asigurat_la_cas">
              </div>
              <div class="sub-item">
                <label for="nume">Nume</label>
                <input type="text" id="nume" name="nume">
              </div>
              <div class="sub-item">
                <label for="prenume">Prenume</label>
                <input type="text" id="prenume" name="prenume">
              </div>
              <div class="sub-item">
                <label for="adresa">Adresa</label>
                <input type="text" id="adresa" name="adresa">
              </div>
              <div class="sub-item">
                <label for="cid_cnp_ce_pass">CID/CNP/CE/PASS</label>
                <input type="text" id="cid_cnp_ce_pass" name="cid_cnp_ce_pass">
              </div>
            </div>
            <div class="grid-item grid-item-2">
              <div class="sub-item">
                <input type="checkbox" id="salariat" name="salariat">
                <label for="salariat">Salariat</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="coasigurat" name="coasigurat">
                <label for="coasigurat">Coasigurat</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="liber_profesionist" name="liber_profesionist">
                <label for="liber_profesionist">Liber-profesionist</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="copil" name="copil">
                <label for="copil">Copil (<18 ani)</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="elev_ucenic_student" name="elev_ucenic_student">
                <label for="elev_ucenic_student">Elev/Ucenic/Student (18-26 ani)</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="gravida_lehuza" name="gravida_lehuza">
                <label for="gravida_lehuza">Gravida/Lehuza</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="pensionar" name="pensionar">
                <label for="pensionar">Pensionar</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="alte_categorii" name="alte_categorii">
                <label for="alte_categorii">Alte categorii</label>
              </div>
            </div>
            <div class="grid-item grid-item-3">
              <div class="sub-item">
                <input type="checkbox" id="veteran" name="veteran">
                <label for="veteran">Veteran</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="revolutionar" name="revolutionar">
                <label for="revolutionar">Revolutionar</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="handicap" name="handicap">
                <label for="handicap">Handicap</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="pns" name="pns">
                <label for="pns">PNS</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="ajutor_social" name="ajutor_social">
                <label for="ajutor_social">Ajutor social</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="somaj" name="somaj">
                <label for="somaj">Somaj</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="cadru_european" name="cadru_european">
                <label for="cadru_european">Cadru European (CE)</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="acorduri_internationale" name="acorduri_internationale">
                <label for="acorduri_internationale">Acorduri internationale</label>
              </div>
            </div>
          </div>
          <div class="grid-row">
            <div class="grid-item grid-item-1">
              <div class="sub-item">
                <p>3. Cod diagnostic</p>
              </div>
              <div class="sub-item">
                <input type="text" id="cod_diagnostic" name="cod_diagnostic">
              </div>
              <div class="sub-item">
                <input type="text" id="cod_diagnostic2" name="cod_diagnostic2">
              </div>
            </div>
            <div class="grid-item grid-item-2">
              <div class="sub-item">
                <br><br>
                <label for="diagnostic">Diagnostic</label>
                <input type="text" id="diagnostic" name="diagnostic" style="margin-left: 30px;">
              </div>
            </div>
          </div>
        </div>
        <div class="single-item">
          <label for="accidente_boli" style="margin-right: 10px;">Accidente de munca/Boli profesionale/Daune</label>
          <input type="checkbox" id="accidente_boli" name="accidente_boli">
        </div>
        <footer>
          <div class="footer-section">
            <div class="footer-left">
              <label for="data_trimiterii">Data trimiterii:</label>
              <input type="date" id="data_trimiterii" name="data_trimiterii">
            </div>
            <div class="footer-center">
              <label for="semnatura_medic">Semnătura medicului:</label>
              <input type="text" id="semnatura_medic" name="semnatura_medic">
            </div>
            <div class="footer-right">
              <label for="cod_parafa">Cod parafa:</label>
              <input type="text" id="cod_parafa" name="cod_parafa">
            </div>
          </div>
        </footer>
        <button type="submit">Submit</button>
      </form>
    </header>
  </div>
  <!-- Modal -->
  <div id="myModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p id="modalMessage"></p>
    </div>
  </div>

  <script>
  document.getElementById('trimitereForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Previne trimiterea formularului și reîncărcarea paginii
    const formData = new FormData(this); // Preia datele formularului

    // Trimite datele către server folosind AJAX
    fetch('submit_investigatii.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      // Afișează mesajul în modal
      document.getElementById('modalMessage').innerText = data.message;
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
