<?php
session_start();
include 'config.php';

// Verificare conexiune
if ($conn->connect_error) {
    die("Conexiune esuata: " . $conn->connect_error);
}

// Preluarea datelor din formular
$serie = $_POST['serie'];
$numar = $_POST['numar'];
$unitate_medicala = $_POST['unitate_medicala'];
$cui = $_POST['cui'];
$cas_contract_conventie = $_POST['cas_contract_conventie'];
$aprobat_comisie_cjas_cnas = isset($_POST['aprobat_comisie_cjas_cnas']) ? 1 : 0;
$nr_decizie = $_POST['nr_decizie'];
$mf = isset($_POST['mf']) ? 1 : 0;
$ambulatoriu = isset($_POST['ambulatoriu']) ? 1 : 0;
$spital = isset($_POST['spital']) ? 1 : 0;
$altele = isset($_POST['altele']) ? 1 : 0;
$mf_mm = isset($_POST['mf_mm']) ? 1 : 0;
$tip = $_POST['tip'];
$nume_asigurat = $_POST['nume_asigurat'];
$prenume_asigurat = $_POST['prenume_asigurat'];
$cid_ce_pass = $_POST['cid_ce_pass'];
$fo_rc = $_POST['fo_rc'];
$data_nasterii = $_POST['data_nasterii'];
$sex = isset($_POST['sex']) && in_array($_POST['sex'], ['M', 'F']) ? $_POST['sex'] : null;
$cetatenia = $_POST['cetatenia'];
$diagnostic_cod_dg = $_POST['diagnostic_cod_dg'];
$data_prescriere = $_POST['data_prescriere'];
$numar_zile_prescriere = $_POST['numar_zile_prescriere'];

$cod1 = $_POST['cod1'];
$tip1 = $_POST['tip1'];
$denumire1 = $_POST['denumire1'];
$ds1 = $_POST['ds1'];
$cantitate1 = $_POST['cantitate1'];
$pret1 = $_POST['pret1'];
$lista1 = $_POST['lista1'];

$cod2 = $_POST['cod2'];
$tip2 = $_POST['tip2'];
$denumire2 = $_POST['denumire2'];
$ds2 = $_POST['ds2'];
$cantitate2 = $_POST['cantitate2'];
$pret2 = $_POST['pret2'];
$lista2 = $_POST['lista2'];

$cod3 = $_POST['cod3'];
$tip3 = $_POST['tip3'];
$denumire3 = $_POST['denumire3'];
$ds3 = $_POST['ds3'];
$cantitate3 = $_POST['cantitate3'];
$pret3 = $_POST['pret3'];
$lista3 = $_POST['lista3'];

$cod4 = $_POST['cod4'];
$tip4 = $_POST['tip4'];
$denumire4 = $_POST['denumire4'];
$ds4 = $_POST['ds4'];
$cantitate4 = $_POST['cantitate4'];
$pret4 = $_POST['pret4'];
$lista4 = $_POST['lista4'];

$nume_parafa_medic_prescriptor = $_POST['nume_parafa_medic_prescriptor'];
$semnatura_medic = $_POST['semnatura_medic'];

// Verifică valoarea sexului
if ($sex === null) {
    die("Eroare: Sexul trebuie să fie 'M' sau 'F'.");
}

// Inserarea datelor în baza de date
$sql = "INSERT INTO retete_compensate (
    serie, numar, unitate_medicala, cui, stat_membru, cas_contract_conventie, aprobat_comisie_cjas_cnas, 
    nr_decizie, mf, ambulatoriu, spital, altele, mf_mm, tip, nume_asigurat, prenume_asigurat, 
    cid_ce_pass, fo_rc, data_nasterii, sex, cetatenia, diagnostic_cod_dg, data_prescriere, 
    numar_zile_prescriere, cod_diag1, tip_diag1, denumire1, ds1, cantitate1, pret_ref1, lista1, 
    cod_diag2, tip_diag2, denumire2, ds2, cantitate2, pret_ref2, lista2, cod_diag3, tip_diag3, 
    denumire3, ds3, cantitate3, pret_ref3, lista3, cod_diag4, tip_diag4, denumire4, ds4, 
    cantitate4, pret_ref4, lista4, nume_parafa_medic_prescriptor, semnatura_medic
) VALUES (
    '$serie', '$numar', '$unitate_medicala', '$cui', 'RO', '$cas_contract_conventie', '$aprobat_comisie_cjas_cnas', 
    '$nr_decizie', '$mf', '$ambulatoriu', '$spital', '$altele', '$mf_mm', '$tip', '$nume_asigurat', 
    '$prenume_asigurat', '$cid_ce_pass', '$fo_rc', '$data_nasterii', '$sex', '$cetatenia', '$diagnostic_cod_dg', 
    '$data_prescriere', '$numar_zile_prescriere', '$cod1', '$tip1', '$denumire1', '$ds1', '$cantitate1', 
    '$pret1', '$lista1', '$cod2', '$tip2', '$denumire2', '$ds2', '$cantitate2', '$pret2', '$lista2', 
    '$cod3', '$tip3', '$denumire3', '$ds3', '$cantitate3', '$pret3', '$lista3', '$cod4', '$tip4', 
    '$denumire4', '$ds4', '$cantitate4', '$pret4', '$lista4', '$nume_parafa_medic_prescriptor', '$semnatura_medic'
)";

if ($conn->query($sql) === TRUE) {
    $_SESSION['message'] = "Înregistrare adăugată cu succes!";
    header("Location: reteta_compensata.php"); // Redirecționare către o pagină de succes
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
