<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

// Verifică dacă datele au fost trimise prin POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preluarea datelor din formular
    $seria = $_POST['seria'];
    $nr = $_POST['nr'];
    $nume = $_POST['nume'];
    $cnp = $_POST['cnp'];
    $cnpCopil = $_POST['cnpCopil'];
    $localitate = $_POST['localitate'];
    $strada = $_POST['strada'];
    $nrS = $_POST['nrS'];
    $bloc = $_POST['bloc'];
    $scara = $_POST['scara'];
    $etaj = $_POST['etaj'];
    $apart = $_POST['apart'];
    $judet = $_POST['judet'];
    $unitatea = $_POST['unitatea'];
    $sectia = $_POST['sectia'];
    $diagnostic = $_POST['diagnostic'];
    $cod_diagnostic = $_POST['cod_diagnostic'];
    $aviz = $_POST['aviz'];
    $deLa = $_POST['deLa'];
    $nrZile = $_POST['nrZile'];
    $panaLa = $_POST['panaLa'];
    $cod_urgenta = $_POST['cod_urgenta'];
    $medic = $_POST['medic'];
    $dataEliberarii = $_POST['dataEliberarii'];

    // Pregătirea și executarea interogării SQL
    $sql = "INSERT INTO concedii_medicale (seria, nr, nume, cnp, cnpCopil, localitate, strada, nrS, bloc, scara, etaj, apart, judet, unitatea, sectia, diagnostic, cod_diagnostic, aviz, deLa, nrZile, panaLa, cod_urgenta, medic, dataEliberarii) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
    $stmt->bind_param('sissssssssssssssssssssss', $seria, $nr, $nume, $cnp, $cnpCopil, $localitate, $strada, $nrS, $bloc, $scara, $etaj, $apart, $judet, $unitatea, $sectia, $diagnostic, $cod_diagnostic, $aviz, $deLa, $nrZile, $panaLa, $cod_urgenta, $medic, $dataEliberarii);
  
    if ($stmt->execute() === TRUE) {
      echo "Înregistrare adăugată cu succes!";
  } else {
      echo "Eroare: " . $stmt->error;
  }

  // Închiderea conexiunii
  $stmt->close();
  $conn->close();
} else {
  echo "Metoda de solicitare nu este permisă.";
}
?>