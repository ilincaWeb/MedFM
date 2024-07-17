<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$prenume = $_SESSION['user'];
$cnp = $_SESSION['cnp'];

require_once 'config.php';
require_once 'functions.php';

// Obținerea asistentului asociat cu medicul pacientului
$stmt = $conn->prepare("
    SELECT a.nume, a.prenume 
    FROM asistenti a
    JOIN pacienti p ON a.medic_id = p.medic_id
    WHERE p.CNP = ?
");
$stmt->bind_param("s", $cnp);
$stmt->execute();
$stmt->bind_result($asistent_nume, $asistent_prenume);
$stmt->fetch();
$stmt->close();

// Obținerea notificărilor pentru mesaje noi și citite de la asistent
$stmt = $conn->prepare("
    SELECT m.cnp, m.prenume, m.nume, MAX(m.data_trimiterii) as data_trimiterii, COUNT(*) as numar_mesaje, m.citit
    FROM mesaje m
    WHERE m.cnp = ? AND m.destinatar = 'pacient'
    GROUP BY m.cnp, m.prenume, m.nume, m.citit
    ORDER BY MAX(m.data_trimiterii) DESC
");
$stmt->bind_param("s", $cnp);
$stmt->execute();
$result = $stmt->get_result();
$notificari = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Funcție pentru formatarea datelor
function format_date($date_str) {
    $timestamp = strtotime($date_str);
    return date('l, d F Y', $timestamp);
}

// Obținerea numărului de notificări necitite din tabelul notificari
$sql = "SELECT COUNT(*) as unread_count 
        FROM notificari n
        JOIN programari p ON n.programare_id = p.id
        WHERE p.pacient_CNP = ? AND n.destinatar = 'pacient' AND n.citit = 0";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('s', $cnp);
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$result = $stmt->get_result();
if (!$result) {
    die("Get result failed: " . $stmt->error);
}
$row = $result->fetch_assoc();
$unread_notifications = $row['unread_count'];

$stmt->close();

// Obținerea numărului de mesaje necitite din tabelul mesaje
$sql = "SELECT COUNT(*) as unread_count 
        FROM mesaje 
        WHERE cnp = ? AND destinatar = 'pacient' AND citit = 0";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('s', $cnp);
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$result = $stmt->get_result();
if (!$result) {
    die("Get result failed: " . $stmt->error);
}
$row = $result->fetch_assoc();
$unread_messages = $row['unread_count'];

$stmt->close();

// Calcularea numărului total de notificări necitite
$total_unread = $unread_notifications + $unread_messages;

// Obținerea primei intrări din tabelul webminarii care nu a fost prezentată
$stmt = $conn->prepare("SELECT id, titlu, descriere FROM webminarii WHERE prezentat = 0 LIMIT 1");
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$stmt->bind_result($webminar_id, $webminar_titlu, $webminar_descriere);
$stmt->fetch();
$stmt->close();

// Memorează id-ul webminarului în sesiune
$_SESSION['webminar_id'] = $webminar_id;

// Calculul locurilor disponibile
$max_locuri = 100;

// Verificăm numărul de înscrieri pentru acest webminar
$stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM inscrieri_webminarii WHERE webminar_id = ?");
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('i', $_SESSION['webminar_id']);
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$nr_inscrisi = $row['cnt'];
$stmt->close();

$locuri_disponibile = $max_locuri - $nr_inscrisi;

// Verifică și procesează formularul de înscriere
$email_err = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $_SESSION['pacient_message'] = "Te rog introdu o adresă de email.";
        $_SESSION['pacient_message_type'] = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['pacient_message'] = "Te rog introdu o adresă de email validă.";
        $_SESSION['pacient_message_type'] = "error";
    } else {
        // Verificarea dacă utilizatorul este deja înscris la acest webminar
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM inscrieri_webminarii WHERE webminar_id = ? AND CNP = ?");
        if (!$stmt) {
            die("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param('is', $_SESSION['webminar_id'], $cnp);
        if (!$stmt->execute()) {
            die("Execute statement failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row['cnt'] > 0) {
            $_SESSION['pacient_message'] = "Deja sunteti inscris la acest webminar!";
            $_SESSION['pacient_message_type'] = "error";
        } elseif ($locuri_disponibile <= 0) {
            $_SESSION['pacient_message'] = "Nu mai sunt locuri disponibile pentru acest webminar!";
            $_SESSION['pacient_message_type'] = "error";
        } else {
            // Inserarea datelor în tabela inscrieri_webminarii
            $stmt = $conn->prepare("INSERT INTO inscrieri_webminarii (webminar_id, CNP, email) VALUES (?, ?, ?)");
            if (!$stmt) {
                die("Prepare statement failed: " . $conn->error);
            }
            $stmt->bind_param('iss', $_SESSION['webminar_id'], $cnp, $email);
            if ($stmt->execute() === TRUE) {
                $stmt->close(); // Închidem statement-ul înainte de a continua
                
                // Actualizarea numărului de înscriși în tabela webminarii
                $stmt = $conn->prepare("UPDATE webminarii SET nr_inscrisi = nr_inscrisi + 1 WHERE id = ?");
                if (!$stmt) {
                    die("Prepare statement failed: " . $conn->error);
                }
                $stmt->bind_param('i', $_SESSION['webminar_id']);
                if ($stmt->execute() === TRUE) {
                    $_SESSION['pacient_message'] = "Ati fost inregistrat pentru participarea la webminar!";
                    $_SESSION['pacient_message_type'] = "success";
                } else {
                    $_SESSION['pacient_message'] = "A apărut o eroare. Vă rugăm să încercați din nou.";
                    $_SESSION['pacient_message_type'] = "error";
                }
                $stmt->close();
            } else {
                $_SESSION['pacient_message'] = "A apărut o eroare. Vă rugăm să încercați din nou.";
                $_SESSION['pacient_message_type'] = "error";
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style/inbox.css">
  <title>Inbox Pacient</title>
  <style>
    .new-message {
      color: red;
      font-style: italic;
      float: right;
    }
    .date-header {
      font-weight: bold;
      margin-top: 20px;
      margin-bottom: 10px;
    }
    .inbox-notification {
      display: block;
      margin-bottom: 10px;
      padding: 10px;
      background: #f9f9f9;
      text-decoration: none;
      color: black;
    }
    .inbox-notification:hover {
      background: #eee;
    }

    .button-container {
      text-align: center;
      margin: 20px 0;
    }
    .button-container button {
      background-color: #ccc;
      border: none;
      color: white;
      padding: 15px 32px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin: 4px 2px;
      cursor: pointer;
    }
    .button-container .active {
      background-color: #555;
    }
    
  </style>
</head>
<body>
<div id="sidebar">
    <ul>
      <li><a href="calendar-pacient.php"><i class="fa fa-calendar fa-sm"></i> Programările Tale</a></li>
      <li><a href="past_medical_records.php"><i class="fa fa-notes-medical fa-sm"></i> Istoric Medical</a></li>
      <li><a href="webminarii.php"><i class="fa fa-graduation-cap fa-sm"></i>Webminarii</a></li>
    </ul>
  </div>
  <nav id="navbar" class="navsticky">
    <div class="container">
      <h1 class="logo"><a href="index.html">MedFM</a></h1>
      <ul>
        <li><a class="current" href="patient-home.php">Acasă</a></li>
        <li class="navbar-item">
          <a href="pacient-inbox.php">Inbox</a>
          <?php if ($total_unread > 0): ?>
            <span class="notification-badge"><?php echo $total_unread; ?></span>
          <?php endif; ?>
        </li>
        <li><a href="#">Bună, <?php echo htmlspecialchars($prenume); ?></a></li>
      </ul>
    </div>
  </nav>
  <div class="container container-home web particip">
    <div class="zoom">
      <h2><?php echo htmlspecialchars($webminar_titlu); ?></h2>
      <p><?php echo htmlspecialchars($webminar_descriere); ?></p>
    </div>
    <div class="inscriere">
      <p>Număr de locuri disponibile: <?php echo $locuri_disponibile; ?></p>
      <?php if (isset($_SESSION['pacient_message'])): ?>
          <div class="message <?php echo $_SESSION['pacient_message_type']; ?>">
              <?php
              echo $_SESSION['pacient_message'];
              unset($_SESSION['pacient_message']);
              unset($_SESSION['pacient_message_type']);
              ?>
          </div>
      <?php endif; ?>
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="email" name="email" placeholder="Introduceți adresa de e-mail">
        <span class="error-message"><?php echo $email_err; ?></span>
        <button type="submit" class="btn btn-dark">Vreau să particip!</button>
      </form>
      <p>Data susținerii webinarului este: <b>20.07.2024</b></p>
    </div>
    <!--<a href="collect_emails.php">aiki</a> -->
  </div>
  <footer id="secondary-footer">
    <div class="footer-elements">
        <div class="footer-item"><a href="about.php">Despre Noi</a></div>
        <div class="footer-item"><a href="contact.php">Contactează-ne</a></div>
        <div class="footer-item"><a href="#" id="gdpr-link">GDPR</a></div>
    </div>
    <p  style="font-weight: bold;" >MedFM &copy; 2024, Toate drepturile rezervate</p>
</footer>
   <!-- Include GDPR Modal -->
   <?php include 'gdpr_modal.php'; ?>  
      
</body>
</html>
