<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
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
  <link rel="stylesheet" href="style/reteta.css">
  <title>Rețetă Compensată</title>
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
  <div class="reteta-container">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var modal = document.getElementById('successModal');
                        modal.style.display = 'block';
                    });
                  </script>";
            ?>
        </div>
    <?php endif; ?>
    <form class="reteta-form" action="submit_reteta.php" method="POST">
      <div class="input-row">
        <div class="input-group">
          <label for="serie">Serie:</label>
          <input type="text" id="serie" name="serie" required>
        </div>
        <div class="input-group">
          <label for="numar">Număr:</label>
          <input type="text" id="numar" name="numar" required>
        </div>
      </div>
      <div class="grid-container">
        <div class="grid-item flex-item-space-between">
          <div class="flex-item-vertical" id="item11">
            <div class="item"><input type="text" name="unitate_medicala" placeholder="Unitate medicala"></div>
            <div class="item">CUI <input type="text" name="cui" style="margin-right: 50px;"> Stat membru: RO</div>
            <div class="item">CAS-Contract/conventie <input type="text" name="cas_contract_conventie"></div>
            <div class="item"><input type="checkbox" name="aprobat_comisie_cjas_cnas"> Aprobat Comisie CJAS/CNAS</div>
            <div class="item">Nr. decizie <input type="text" name="nr_decizie"></div>
          </div>
          <div class="flex-item-vertical" id="item12">
            <div class="item"><input type="checkbox" name="mf"> MF</div>
            <div class="item"><input type="checkbox" name="ambulatoriu"> AMBULATORIU</div>
            <div class="item"><input type="checkbox" name="spital"> SPITAL</div>
            <div class="item"><input type="checkbox" name="altele"> ALTELE</div>
            <div class="item"><input type="checkbox" name="mf_mm"> MF-MM</div>
          </div>
        </div>
        <div class="grid-item flex-item-space-between">
          <div class="flex-item-vertical" id="item21">
            <div class="item">Asigurat <span style="margin-right: 30px;"></span> Tip <input type="text" name="tip"></div>
            <div class="item">Nume <input type="text" name="nume_asigurat"></div>
            <div class="item">Prenume <input type="text" name="prenume_asigurat"></div>
            <div class="item">CID/CE/PASS <input type="text" name="cid_ce_pass"></div>
          </div>
          <div class="flex-item-vertical" id="item22">
            <div class="item">FO/RC <input type="text" name="fo_rc"></div>
            <div class="item">Data nasterii <input type="date" name="data_nasterii"></div>
            <div class="item">Sexul <span style="margin-right: 10px;"></span><input type="checkbox" name="sex" value="M" id="sexM" onclick="toggleSex('M')"> M <span style="margin-right: 10px;"></span><input type="checkbox" name="sex" value="F" id="sexF" onclick="toggleSex('F')"> F</div>
            <div class="item">Cetatenia <input type="text" name="cetatenia"></div>
          </div>
        </div>
        <div class="grid-item" id="item31">Diagnostic/Cod Dg. <input type="text" name="diagnostic_cod_dg"></div>
        <div class="grid-item flex-item-space-between">
          <div class="item" id="item41">Data prescriere <input type="date" name="data_prescriere"></div>
          <div class="item" id="item42">Numar zile prescriere: <input type="text" name="numar_zile_prescriere"></div>
        </div>
        <div class="grid-item">
          <div class="table-grid">
            <div class="col1 header vertical-text">Poz</div>
            <div class="col2 header column-text">Cod diag.</div>
            <div class="col3 header column-text">Tip diag.</div>
            <div class="col4 header complex-header">Denumire comuna internationala / Denumire comerciala <br/> / FF / Concentratie</div>
            <div class="col5 header">D.S.</div>
            <div class="col6 header">Cantitate</div>
            <div class="col7 header column-text">%Pret<br/>ref.</div>
            <div class="col8 header">Lista</div>
            <!-- Add rows here -->
            <div class="col1">1.</div>
            <div class="col2"><input type="text" name="cod1"></div>
            <div class="col3"><input type="text" name="tip1"></div>
            <div class="col4"><input type="text" name="denumire1"></div>
            <div class="col5"><input type="text" name="ds1"></div>
            <div class="col6"><input type="text" name="cantitate1"></div>
            <div class="col7"><input type="text" name="pret1"></div>
            <div class="col8"><input type="text" name="lista1"></div>
            <div class="col1">2.</div>
            <div class="col2"><input type="text" name="cod2"></div>
            <div class="col3"><input type="text" name="tip2"></div>
            <div class="col4"><input type="text" name="denumire2"></div>
            <div class="col5"><input type="text" name="ds2"></div>
            <div class="col6"><input type="text" name="cantitate2"></div>
            <div class="col7"><input type="text" name="pret2"></div>
            <div class="col8"><input type="text" name="lista2"></div>
            <div class="col1">3.</div>
            <div class="col2"><input type="text" name="cod3"></div>
            <div class="col3"><input type="text" name="tip3"></div>
            <div class="col4"><input type="text" name="denumire3"></div>
            <div class="col5"><input type="text" name="ds3"></div>
            <div class="col6"><input type="text" name="cantitate3"></div>
            <div class="col7"><input type="text" name="pret3"></div>
            <div class="col8"><input type="text" name="lista3"></div>
            <div class="col1">4.</div>
            <div class="col2"><input type="text" name="cod4"></div>
            <div class="col3"><input type="text" name="tip4"></div>
            <div class="col4"><input type="text" name="denumire4"></div>
            <div class="col5"><input type="text" name="ds4"></div>
            <div class="col6"><input type="text" name="cantitate4"></div>
            <div class="col7"><input type="text" name="pret4"></div>
            <div class="col8"><input type="text" name="lista4"></div>
          </div>
        </div>
        <div class="grid-item flex-item-space-between">
          <div class="flex-item-vertical" id="item61">
            <div class="item">Nume/Parafa medic prescriptor</div>
            <div class="item"><input type="text" name="nume_parafa_medic_prescriptor"></div>
            <div class="item">Semnatura: <input type="text" name="semnatura_medic"></div>
          </div>
          <div class="item" id="item62">L.S. Medic</div>
        </div>
      </div>
      <div class="submit-container">
        <button type="submit">Submit</button>
      </div>
    </form>
  </div>
  <!-- Modal -->
  <div id="successModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p>Înregistrare adăugată cu succes!</p>
    </div>
  </div>
  <script>
    function toggleSex(selected) {
      document.getElementById('sexM').checked = selected === 'M';
      document.getElementById('sexF').checked = selected === 'F';
    }

    document.addEventListener('DOMContentLoaded', function() {
      var modal = document.getElementById('successModal');
      var span = document.getElementsByClassName('close')[0];

      span.onclick = function() {
        modal.style.display = 'none';
      }

      window.onclick = function(event) {
        if (event.target == modal) {
          modal.style.display = 'none';
        }
      }

      <?php if (isset($_SESSION['message'])): ?>
        modal.style.display = 'block';
        <?php unset($_SESSION['message']); ?>
      <?php endif; ?>
    });
  </script>
</body>
</html>
