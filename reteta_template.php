<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rețetă Compensată</title>
  <link rel="stylesheet" href="style/reteta.css">
</head>
<body>
  <div id="sidebar">
    <ul>
      <li><a href="patient-home.html"><i class="fa fa-user fa-sm"></i> Profile</a></li>
      <li><a href="#"><i class="fa fa-calendar fa-sm"></i> Your Appointments</a></li>
      <li><a href="medical-records.html"><i class="fa fa-notes-medical fa-sm"></i> Medical Records</a></li>
      <li><a href="#">Item 4</a></li>
    </ul>
  </div>

  <nav id="navbar" class="navsticky">
    <div class="container medical-container">
      <h1 class="logo"><a href="index.html">MedFM</a></h1>
      <ul>
        <li><a class="current" href="index.html">Home</a></li>
        <li><a href="contact.html">Contactează-ne</a></li>
      </ul>
    </div>
  </nav>

  <div class="reteta-container">
    <h2>Rețetă Compensată</h2>
    <form class="reteta-form">
      <div class="input-row">
        <div class="input-group">
          <label for="serie">Serie:</label>
          <input type="text" id="serie" name="serie" value="<?php echo htmlspecialchars($form_data['serie']); ?>" readonly>
        </div>
        <div class="input-group">
          <label for="numar">Număr:</label>
          <input type="text" id="numar" name="numar" value="<?php echo htmlspecialchars($form_data['numar']); ?>" readonly>
        </div>
      </div>
      <div class="grid-container">
        <div class="grid-item flex-item-space-between">
          <div class="flex-item-vertical" id="item11">
            <div class="item"><input type="text" name="unitate_medicala" value="<?php echo htmlspecialchars($form_data['unitate_medicala']); ?>" readonly></div>
            <div class="item">CUI <input type="text" name="cui" value="<?php echo htmlspecialchars($form_data['cui']); ?>" readonly> Stat membru: RO</div>
            <div class="item">CAS-Contract/conventie <input type="text" name="cas_contract_conventie" value="<?php echo htmlspecialchars($form_data['cas_contract_conventie']); ?>" readonly></div>
            <div class="item"><input type="checkbox" name="aprobat_comisie_cjas_cnas" <?php echo $form_data['aprobat_comisie_cjas_cnas'] ? 'checked' : ''; ?> disabled> Aprobat Comisie CJAS/CNAS</div>
            <div class="item">Nr. decizie <input type="text" name="nr_decizie" value="<?php echo htmlspecialchars($form_data['nr_decizie']); ?>" readonly></div>
          </div>
          <div class="flex-item-vertical" id="item12">
            <div class="item"><input type="checkbox" name="mf" <?php echo $form_data['mf'] ? 'checked' : ''; ?> disabled> MF</div>
            <div class="item"><input type="checkbox" name="ambulatoriu" <?php echo $form_data['ambulatoriu'] ? 'checked' : ''; ?> disabled> AMBULATORIU</div>
            <div class="item"><input type="checkbox" name="spital" <?php echo $form_data['spital'] ? 'checked' : ''; ?> disabled> SPITAL</div>
            <div class="item"><input type="checkbox" name="altele" <?php echo $form_data['altele'] ? 'checked' : ''; ?> disabled> ALTELE</div>
            <div class="item"><input type="checkbox" name="mf_mm" <?php echo $form_data['mf_mm'] ? 'checked' : ''; ?> disabled> MF-MM</div>
          </div>
        </div>
        <div class="grid-item flex-item-space-between">
          <div class="flex-item-vertical" id="item21">
            <div class="item">Asigurat <span style="margin-right: 30px;"></span> Tip <input type="text" name="tip" value="<?php echo htmlspecialchars($form_data['tip']); ?>" readonly></div>
            <div class="item">Nume <input type="text" name="nume_asigurat" value="<?php echo htmlspecialchars($form_data['nume_asigurat']); ?>" readonly></div>
            <div class="item">Prenume <input type="text" name="prenume_asigurat" value="<?php echo htmlspecialchars($form_data['prenume_asigurat']); ?>" readonly></div>
            <div class="item">CID/CE/PASS <input type="text" name="cid_ce_pass" value="<?php echo htmlspecialchars($form_data['cid_ce_pass']); ?>" readonly></div>
          </div>
          <div class="flex-item-vertical" id="item22">
            <div class="item">FO/RC <input type="text" name="fo_rc" value="<?php echo htmlspecialchars($form_data['fo_rc']); ?>" readonly></div>
            <div class="item">Data nasterii <input type="date" name="data_nasterii" value="<?php echo htmlspecialchars($form_data['data_nasterii']); ?>" readonly></div>
            <div class="item">Sexul <span style="margin-right: 10px;"></span><input type="checkbox" name="sex" value="M" <?php echo ($form_data['sex'] == 'M') ? 'checked' : ''; ?> disabled> M <span style="margin-right: 10px;"></span><input type="checkbox" name="sex" value="F" <?php echo ($form_data['sex'] == 'F') ? 'checked' : ''; ?> disabled> F</div>
            <div class="item">Cetatenia <input type="text" name="cetatenia" value="<?php echo htmlspecialchars($form_data['cetatenia']); ?>" readonly></div>
          </div>
        </div>
        <div class="grid-item" id="item31">Diagnostic/Cod Dg. <input type="text" name="diagnostic_cod_dg" value="<?php echo htmlspecialchars($form_data['diagnostic_cod_dg']); ?>" readonly></div>
        <div class="grid-item flex-item-space-between">
          <div class="item" id="item41">Data prescriere <input type="date" name="data_prescriere" value="<?php echo htmlspecialchars($form_data['data_prescriere']); ?>" readonly></div>
          <div class="item" id="item42">Numar zile prescriere: <input type="text" name="numar_zile_prescriere" value="<?php echo htmlspecialchars($form_data['numar_zile_prescriere']); ?>" readonly></div>
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
            <div class="col1">1.</div>
            <div class="col2"><input type="text" name="cod1" value="<?php echo htmlspecialchars($form_data['cod_diag1']); ?>" readonly></div>
            <div class="col3"><input type="text" name="tip1" value="<?php echo htmlspecialchars($form_data['tip_diag1']); ?>" readonly></div>
            <div class="col4"><input type="text" name="denumire1" value="<?php echo htmlspecialchars($form_data['denumire1']); ?>" readonly></div>
            <div class="col5"><input type="text" name="ds1" value="<?php echo htmlspecialchars($form_data['ds1']); ?>" readonly></div>
            <div class="col6"><input type="text" name="cantitate1" value="<?php echo htmlspecialchars($form_data['cantitate1']); ?>" readonly></div>
            <div class="col7"><input type="text" name="pret1" value="<?php echo htmlspecialchars($form_data['pret_ref1']); ?>" readonly></div>
            <div class="col8"><input type="text" name="lista1" value="<?php echo htmlspecialchars($form_data['lista1']); ?>" readonly></div>
            <div class="col1">2.</div>
            <div class="col2"><input type="text" name="cod2" value="<?php echo htmlspecialchars($form_data['cod_diag2']); ?>" readonly></div>
            <div class="col3"><input type="text" name="tip2" value="<?php echo htmlspecialchars($form_data['tip_diag2']); ?>" readonly></div>
            <div class="col4"><input type="text" name="denumire2" value="<?php echo htmlspecialchars($form_data['denumire2']); ?>" readonly></div>
            <div class="col5"><input type="text" name="ds2" value="<?php echo htmlspecialchars($form_data['ds2']); ?>" readonly></div>
            <div class="col6"><input type="text" name="cantitate2" value="<?php echo htmlspecialchars($form_data['cantitate2']); ?>" readonly></div>
            <div class="col7"><input type="text" name="pret2" value="<?php echo htmlspecialchars($form_data['pret_ref2']); ?>" readonly></div>
            <div class="col8"><input type="text" name="lista2" value="<?php echo htmlspecialchars($form_data['lista2']); ?>" readonly></div>
            <div class="col1">3.</div>
            <div class="col2"><input type="text" name="cod3" value="<?php echo htmlspecialchars($form_data['cod_diag3']); ?>" readonly></div>
            <div class="col3"><input type="text" name="tip3" value="<?php echo htmlspecialchars($form_data['tip_diag3']); ?>" readonly></div>
            <div class="col4"><input type="text" name="denumire3" value="<?php echo htmlspecialchars($form_data['denumire3']); ?>" readonly></div>
            <div class="col5"><input type="text" name="ds3" value="<?php echo htmlspecialchars($form_data['ds3']); ?>" readonly></div>
            <div class="col6"><input type="text" name="cantitate3" value="<?php echo htmlspecialchars($form_data['cantitate3']); ?>" readonly></div>
            <div class="col7"><input type="text" name="pret3" value="<?php echo htmlspecialchars($form_data['pret_ref3']); ?>" readonly></div>
            <div class="col8"><input type="text" name="lista3" value="<?php echo htmlspecialchars($form_data['lista3']); ?>" readonly></div>
            <div class="col1">4.</div>
            <div class="col2"><input type="text" name="cod4" value="<?php echo htmlspecialchars($form_data['cod_diag4']); ?>" readonly></div>
            <div class="col3"><input type="text" name="tip4" value="<?php echo htmlspecialchars($form_data['tip_diag4']); ?>" readonly></div>
            <div class="col4"><input type="text" name="denumire4" value="<?php echo htmlspecialchars($form_data['denumire4']); ?>" readonly></div>
            <div class="col5"><input type="text" name="ds4" value="<?php echo htmlspecialchars($form_data['ds4']); ?>" readonly></div>
            <div class="col6"><input type="text" name="cantitate4" value="<?php echo htmlspecialchars($form_data['cantitate4']); ?>" readonly></div>
            <div class="col7"><input type="text" name="pret4" value="<?php echo htmlspecialchars($form_data['pret_ref4']); ?>" readonly></div>
            <div class="col8"><input type="text" name="lista4" value="<?php echo htmlspecialchars($form_data['lista4']); ?>" readonly></div>
          </div>
        </div>
        <div class="grid-item flex-item-space-between">
          <div class="flex-item-vertical" id="item61">
            <div class="item">Nume/Parafa medic prescriptor</div>
            <div class="item"><input type="text" name="nume_parafa_medic_prescriptor" value="<?php echo htmlspecialchars($form_data['nume_parafa_medic_prescriptor']); ?>" readonly></div>
            <div class="item">Semnatura: <input type="text" name="semnatura_medic" value="<?php echo htmlspecialchars($form_data['semnatura_medic']); ?>" readonly></div>
          </div>
          <div class="item" id="item62">L.S. Medic</div>
        </div>
      </div>
      <div class="submit-container">
        <button type="button" onclick="window.history.back()">Back</button>
      </div>
    </form>
  </div>
</body>
</html>
