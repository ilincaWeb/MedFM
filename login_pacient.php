<?php
session_start();
include 'config.php';

// Verifică dacă datele au fost trimise prin POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cnp = $_POST['username'];
    $an_nastere = $_POST['password'];

    // Pregătește și execută interogarea SQL
    $sql = "SELECT * FROM pacienti WHERE CNP = ? AND YEAR(data_nasterii) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $cnp, $an_nastere);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // CNP și anul nașterii coincid
        $row = $result->fetch_assoc();
        $_SESSION['user'] = $row['prenume']; // Setează prenumele în sesiune
        $_SESSION['cnp'] = $row['CNP']; // Setează CNP-ul în sesiune

        // Redirecționare către pagina patient-home.php
        header("Location: patient-home.php");
        exit(); // Asigură-te că scriptul se oprește după redirecționare
    } else {
        // CNP sau anul nașterii incorect
        echo "<script>alert('CNP-ul sau anul nașterii este incorect.'); window.location.href='login-pacient.html';</script>";
        exit();
    }

    // Închiderea conexiunii
    $stmt->close();
    $conn->close();
} else {
    echo "Metoda de solicitare nu este permisă.";
}
?>
