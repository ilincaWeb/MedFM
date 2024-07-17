<?php
include 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preluarea datelor din formular
    $serie = $_POST['serie'] ?? '';
    $numar = $_POST['numar'] ?? '';
    $unitate_medicala = $_POST['unitate_medicala'] ?? '';
    $cui = $_POST['cui'] ?? '';
    $sediu = $_POST['sediu'] ?? '';
    $judetul = $_POST['judetul'] ?? '';
    $casa_asigurari = $_POST['casa_asigurari'] ?? '';
    $nr_contract = $_POST['nr_contract'] ?? '';
    $mf = isset($_POST['mf']) ? 1 : 0;
    $amb_spec = isset($_POST['amb_spec']) ? 1 : 0;
    $unitate_sanitara = isset($_POST['unitate_sanitara']) ? 1 : 0;
    $altele = isset($_POST['altele']) ? 1 : 0;
    $prioritate_urgenta = isset($_POST['prioritate_urgenta']) ? 1 : 0;
    $prioritate_curente = isset($_POST['prioritate_curente']) ? 1 : 0;
    $asigurat_la_cas = $_POST['asigurat_la_cas'] ?? '';
    $nume = $_POST['nume'] ?? '';
    $prenume = $_POST['prenume'] ?? '';
    $adresa = $_POST['adresa'] ?? '';
    $cid_cnp_ce_pass = $_POST['cid_cnp_ce_pass'] ?? '';
    $salariat = isset($_POST['salariat']) ? 1 : 0;
    $coasigurat = isset($_POST['coasigurat']) ? 1 : 0;
    $liber_profesionist = isset($_POST['liber_profesionist']) ? 1 : 0;
    $copil = isset($_POST['copil']) ? 1 : 0;
    $elev_ucenic_student = isset($_POST['elev_ucenic_student']) ? 1 : 0;
    $gravida_lehuza = isset($_POST['gravida_lehuza']) ? 1 : 0;
    $pensionar = isset($_POST['pensionar']) ? 1 : 0;
    $alte_categorii = isset($_POST['alte_categorii']) ? 1 : 0;
    $veteran = isset($_POST['veteran']) ? 1 : 0;
    $revolutionar = isset($_POST['revolutionar']) ? 1 : 0;
    $handicap = isset($_POST['handicap']) ? 1 : 0;
    $pns = isset($_POST['pns']) ? 1 : 0;
    $ajutor_social = isset($_POST['ajutor_social']) ? 1 : 0;
    $somaj = isset($_POST['somaj']) ? 1 : 0;
    $cadru_european = isset($_POST['cadru_european']) ? 1 : 0;
    $acorduri_internationale = isset($_POST['acorduri_internationale']) ? 1 : 0;
    $cod_diagnostic = $_POST['cod_diagnostic'] ?? '';
    $cod_diagnostic2 = $_POST['cod_diagnostic2'] ?? '';
    $diagnostic = $_POST['diagnostic'] ?? '';
    $accidente_boli = isset($_POST['accidente_boli']) ? 1 : 0;
    $data_trimiterii = $_POST['data_trimiterii'] ?? '';
    $semnatura_medic = $_POST['semnatura_medic'] ?? '';
    $cod_parafa = $_POST['cod_parafa'] ?? '';

    // Debugging: Verificare număr de parametri
    $params = array(
        $serie, $numar, $unitate_medicala, $cui, $sediu, $judetul, $casa_asigurari, $nr_contract, 
        $mf, $amb_spec, $unitate_sanitara, $altele, $prioritate_urgenta, $prioritate_curente, 
        $asigurat_la_cas, $nume, $prenume, $adresa, $cid_cnp_ce_pass, $salariat, $coasigurat, 
        $liber_profesionist, $copil, $elev_ucenic_student, $gravida_lehuza, $pensionar, $alte_categorii, 
        $veteran, $revolutionar, $handicap, $pns, $ajutor_social, $somaj, $cadru_european, 
        $acorduri_internationale, $cod_diagnostic, $cod_diagnostic2, $diagnostic, $accidente_boli, 
        $data_trimiterii, $semnatura_medic, $cod_parafa
    );

    if (count($params) != 42) {
        echo json_encode(['message' => 'Numărul de parametri nu se potrivește.']);
        exit();
    }

    // Inserarea datelor în baza de date
    $sql = "INSERT INTO trimiteri (
        serie, numar, unitate_medicala, cui, sediu, judetul, casa_asigurari, nr_contract, mf, amb_spec, 
        unitate_sanitara, altele, prioritate_urgenta, prioritate_curente, asigurat_la_cas, nume, prenume, 
        adresa, cid_cnp_ce_pass, salariat, coasigurat, liber_profesionist, copil, elev_ucenic_student, 
        gravida_lehuza, pensionar, alte_categorii, veteran, revolutionar, handicap, pns, ajutor_social, 
        somaj, cadru_european, acorduri_internationale, cod_diagnostic, cod_diagnostic2, diagnostic, 
        accidente_boli, data_trimiterii, semnatura_medic, cod_parafa
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['message' => 'Prepare failed: ' . htmlspecialchars($conn->error)]);
        exit();
    }

    $stmt->bind_param(
        'ssssssssssssssssssssssssssssssssssssssssss', 
        $serie, $numar, $unitate_medicala, $cui, $sediu, $judetul, $casa_asigurari, $nr_contract, 
        $mf, $amb_spec, $unitate_sanitara, $altele, $prioritate_urgenta, $prioritate_curente, 
        $asigurat_la_cas, $nume, $prenume, $adresa, $cid_cnp_ce_pass, $salariat, $coasigurat, 
        $liber_profesionist, $copil, $elev_ucenic_student, $gravida_lehuza, $pensionar, $alte_categorii, 
        $veteran, $revolutionar, $handicap, $pns, $ajutor_social, $somaj, $cadru_european, 
        $acorduri_internationale, $cod_diagnostic, $cod_diagnostic2, $diagnostic, $accidente_boli, 
        $data_trimiterii, $semnatura_medic, $cod_parafa
    );

    if ($stmt->execute() === TRUE) {
        echo json_encode(['message' => 'Înregistrare adăugată cu succes!']);
    } else {
        echo json_encode(['message' => 'Eroare: ' . $stmt->error]);
    }

    // Închiderea conexiunii
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['message' => 'Metoda de solicitare nu este permisă.']);
}
?>
