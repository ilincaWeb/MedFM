<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verifică în tabelul medici
    $sql = "SELECT * FROM medici WHERE CNP='$username'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    // Dacă nu există în tabelul medici, verifică în tabelul asistenti
    if (!$user) {
        $sql = "SELECT * FROM asistenti WHERE CNP='$username'";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        $role = 'asistent';
    } else {
        $role = 'doctor';
    }

    // Dacă utilizatorul este găsit
    if ($user) {
        // Dacă parola nu este setată, setează parola
        if (isset($_POST['create_password']) && (is_null($user['parola']) || $user['parola'] == '')) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            if ($role == 'asistent') {
                $update_sql = "UPDATE asistenti SET parola='$hashed_password' WHERE CNP='$username'";
            } else {
                $update_sql = "UPDATE medici SET parola='$hashed_password' WHERE CNP='$username'";
            }
            if ($conn->query($update_sql) === TRUE) {
                $_SESSION['user'] = $user['prenume'];
                $_SESSION['username'] = $username;
                $_SESSION['medic_id'] = $user['id'];
                $_SESSION['cnp'] = $username;
                if ($role == 'asistent') {
                    header("Location: asistent-home.php");
                } else {
                    header("Location: calendar-doctor.php");
                }
            } else {
                echo "Eroare la actualizarea parolei: " . $conn->error;
            }
        } else {
            // Verifică parola
            if (password_verify($password, $user['parola'])) {
                $_SESSION['user'] = $user['prenume'];
                $_SESSION['username'] = $username;
                $_SESSION['medic_id'] = $user['id'];
                $_SESSION['cnp'] = $username;
                if ($role == 'asistent') {
                    header("Location: asistent-home.php");
                } else {
                    header("Location: calendar-doctor.php");
                }
            } else {
                echo "Parola incorectă.";
            }
        }
    } else {
        echo "Utilizatorul nu a fost găsit.";
    }
}

$conn->close();
?>
