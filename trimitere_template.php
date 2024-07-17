<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bilet Trimitere pentru Investigații Paraclinice</title>
  <link rel="stylesheet" href="style/trimitere.css">
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

  <div class="certificate-container container-home">
    <header>
      <h1>Bilet trimitere pentru investigații paraclinice decontate de CAS</h1>
      <form>
        <div class="serie-nr">
          <label for="serie">Serie:</label>
          <input type="text" id="serie" name="serie" value="<?php echo htmlspecialchars($form_data['serie']); ?>" readonly>
          <label for="numar">Nr.:</label>
          <input type="text" id="numar" name="numar" value="<?php echo htmlspecialchars($form_data['numar']); ?>" readonly>
        </div>
        <div class="grid-container">
          <div class="grid-row">
            <div class="grid-item grid-item-1">
              <div class="sub-item">
                <label for="unitate_medicala">1. Unitate medicala</label>
                <input type="text" id="unitate_medicala" name="unitate_medicala" value="<?php echo htmlspecialchars($form_data['unitate_medicala']); ?>" readonly>
              </div>
              <div class="sub-item">
                <label for="cui">CUI</label>
                <input type="text" id="cui" name="cui" value="<?php echo htmlspecialchars($form_data['cui']); ?>" readonly>
              </div>
              <div class="sub-item">
                <label for="sediu">Sediu (localitate, str., nr.)</label>
                <input type="text" id="sediu" name="sediu" value="<?php echo htmlspecialchars($form_data['sediu']); ?>" readonly>
              </div>
              <div class="sub-item">
                <label for="judetul">Judetul</label>
                <input type="text" id="judetul" name="judetul" value="<?php echo htmlspecialchars($form_data['judetul']); ?>" readonly>
              </div>
              <div class="sub-item">
                <label for="casa_asigurari">Casa de asigurari:</label>
                <input type="text" id="casa_asigurari" name="casa_asigurari" value="<?php echo htmlspecialchars($form_data['casa_asigurari']); ?>" readonly>
              </div>
              <div class="sub-item">
                <label for="nr_contract">Nr. contract/conventie</label>
                <input type="text" id="nr_contract" name="nr_contract" value="<?php echo htmlspecialchars($form_data['nr_contract']); ?>" readonly>
              </div>
            </div>
            <div class="grid-item grid-item-2">
              <div class="sub-item">
                <input type="checkbox" id="mf" name="mf" <?php echo $form_data['mf'] ? 'checked' : ''; ?> disabled>
                <label for="mf">MF</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="amb_spec" name="amb_spec" <?php echo $form_data['amb_spec'] ? 'checked' : ''; ?> disabled>
                <label for="amb_spec">Amb. Spec.</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="unitate_sanitara" name="unitate_sanitara" <?php echo $form_data['unitate_sanitara'] ? 'checked' : ''; ?> disabled>
                <label for="unitate_sanitara">Unitate sanitara cu paturi</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="altele" name="altele" <?php echo $form_data['altele'] ? 'checked' : ''; ?> disabled>
                <label for="altele">Altele</label>
              </div>
            </div>
            <div class="grid-item grid-item-3">
              <div class="sub-item">
                <p>Nivel de prioritate</p>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="prioritate_urgenta" name="prioritate_urgenta" <?php echo $form_data['prioritate_urgenta'] ? 'checked' : ''; ?> disabled>
                <label for="prioritate_urgenta">Urgenta</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="prioritate_curente" name="prioritate_curente" <?php echo $form_data['prioritate_curente'] ? 'checked' : ''; ?> disabled>
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
                <input type="text" id="asigurat_la_cas" name="asigurat_la_cas" value="<?php echo htmlspecialchars($form_data['asigurat_la_cas']); ?>" readonly>
              </div>
              <div class="sub-item">
                <label for="nume">Nume</label>
                <input type="text" id="nume" name="nume" value="<?php echo htmlspecialchars($form_data['nume']); ?>" readonly>
              </div>
              <div class="sub-item">
                <label for="prenume">Prenume</label>
                <input type="text" id="prenume" name="prenume" value="<?php echo htmlspecialchars($form_data['prenume']); ?>" readonly>
              </div>
              <div class="sub-item">
                <label for="adresa">Adresa</label>
                <input type="text" id="adresa" name="adresa" value="<?php echo htmlspecialchars($form_data['adresa']); ?>" readonly>
              </div>
              <div class="sub-item">
                <label for="cid_cnp_ce_pass">CID/CNP/CE/PASS</label>
                <input type="text" id="cid_cnp_ce_pass" name="cid_cnp_ce_pass" value="<?php echo htmlspecialchars($form_data['cid_cnp_ce_pass']); ?>" readonly>
              </div>
            </div>
            <div class="grid-item grid-item-2">
              <div class="sub-item">
                <input type="checkbox" id="salariat" name="salariat" <?php echo $form_data['salariat'] ? 'checked' : ''; ?> disabled>
                <label for="salariat">Salariat</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="coasigurat" name="coasigurat" <?php echo $form_data['coasigurat'] ? 'checked' : ''; ?> disabled>
                <label for="coasigurat">Coasigurat</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="liber_profesionist" name="liber_profesionist" <?php echo $form_data['liber_profesionist'] ? 'checked' : ''; ?> disabled>
                <label for="liber_profesionist">Liber-profesionist</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="copil" name="copil" <?php echo $form_data['copil'] ? 'checked' : ''; ?> disabled>
                <label for="copil">Copil (<18 ani)</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="elev_ucenic_student" name="elev_ucenic_student" <?php echo $form_data['elev_ucenic_student'] ? 'checked' : ''; ?> disabled>
                <label for="elev_ucenic_student">Elev/Ucenic/Student (18-26 ani)</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="gravida_lehuza" name="gravida_lehuza" <?php echo $form_data['gravida_lehuza'] ? 'checked' : ''; ?> disabled>
                <label for="gravida_lehuza">Gravida/Lehuza</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="pensionar" name="pensionar" <?php echo $form_data['pensionar'] ? 'checked' : ''; ?> disabled>
                <label for="pensionar">Pensionar</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="alte_categorii" name="alte_categorii" <?php echo $form_data['alte_categorii'] ? 'checked' : ''; ?> disabled>
                <label for="alte_categorii">Alte categorii</label>
              </div>
            </div>
            <div class="grid-item grid-item-3">
              <div class="sub-item">
                <input type="checkbox" id="veteran" name="veteran" <?php echo $form_data['veteran'] ? 'checked' : ''; ?> disabled>
                <label for="veteran">Veteran</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="revolutionar" name="revolutionar" <?php echo $form_data['revolutionar'] ? 'checked' : ''; ?> disabled>
                <label for="revolutionar">Revolutionar</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="handicap" name="handicap" <?php echo $form_data['handicap'] ? 'checked' : ''; ?> disabled>
                <label for="handicap">Handicap</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="pns" name="pns" <?php echo $form_data['pns'] ? 'checked' : ''; ?> disabled>
                <label for="pns">PNS</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="ajutor_social" name="ajutor_social" <?php echo $form_data['ajutor_social'] ? 'checked' : ''; ?> disabled>
                <label for="ajutor_social">Ajutor social</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="somaj" name="somaj" <?php echo $form_data['somaj'] ? 'checked' : ''; ?> disabled>
                <label for="somaj">Somaj</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="cadru_european" name="cadru_european" <?php echo $form_data['cadru_european'] ? 'checked' : ''; ?> disabled>
                <label for="cadru_european">Cadru European (CE)</label>
              </div>
              <div class="sub-item">
                <input type="checkbox" id="acorduri_internationale" name="acorduri_internationale" <?php echo $form_data['acorduri_internationale'] ? 'checked' : ''; ?> disabled>
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
                <input type="text" id="cod_diagnostic" name="cod_diagnostic" value="<?php echo htmlspecialchars($form_data['cod_diagnostic']); ?>" readonly>
              </div>
              <div class="sub-item">
                <input type="text" id="cod_diagnostic2" name="cod_diagnostic2" value="<?php echo htmlspecialchars($form_data['cod_diagnostic2']); ?>" readonly>
              </div>
            </div>
            <div class="grid-item grid-item-2">
              <div class="sub-item">
                <BR><BR></BR></BR>
                <label for="diagnostic">Diagnostic</label>
                <input type="text" id="diagnostic" name="diagnostic" value="<?php echo htmlspecialchars($form_data['diagnostic']); ?>" readonly>
              </div>
            </div>
          </div>
        </div>
        <div class="single-item">
          <label for="item4" style="margin-right: 10px;">Accidente de munca/Boli profesionale/Daune</label>
          <input type="checkbox" id="item4" name="item4" <?php echo $form_data['item4'] ? 'checked' : ''; ?> disabled>
        </div>
        <footer>
          <div class="footer-section">
            <div class="footer-left">
              <label for="data_trimiterii">Data trimiterii:</label>
              <input type="text" id="data_trimiterii" name="data_trimiterii" value="<?php echo htmlspecialchars($form_data['data_trimiterii']); ?>" readonly>
            </div>
            <div class="footer-center">
              <label for="semnatura_medic">Semnătura medicului:</label>
              <input type="text" id="semnatura_medic" name="semnatura_medic" value="<?php echo htmlspecialchars($form_data['semnatura_medic']); ?>" readonly>
            </div>
            <div class="footer-right">
              <label for="cod_parafa">Cod parafa:</label>
              <input type="text" id="cod_parafa" name="cod_parafa" value="<?php echo htmlspecialchars($form_data['cod_parafa']); ?>" readonly>
            </div>
          </div>
        </footer>
      </form>
    </header>
  </div>

  <footer id="secondary-footer">
    <p>MedFM &copy; 2023, All Rights Reserved</p>
  </footer>
</body>
</html>
