<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login-pacient.html");
    exit();
}

$prenume = $_SESSION['user'];
$cnp = $_SESSION['cnp'];

require_once 'config.php';

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://kit.fontawesome.com/a7bbb09be2.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style/about.css">
  <title>Home</title>
</head>
<body>
  <div id="sidebar">
    <ul>
      <li><a href="#"><i class="fa fa-user fa-sm"></i> Profil</a></li>
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

  <div class="container about">
    <h1>Bine ați venit la <span style="color: #79AC78;">MedFM</span>!</h1>
    <p>MedFM este o aplicație revoluționară creată pentru a facilita gestionarea sănătății dumneavoastră și pentru a aduce serviciile medicale mai aproape de utilizatori. Suntem dedicați îmbunătățirii experienței pacienților și eficientizării comunicării între pacienți și cadrele medicale.</p>

    <h2>Misiunea Noastră</h2>
    <p>Misiunea noastră este să oferim un sistem medical integrat, accesibil și eficient, care să îmbunătățească sănătatea și bunăstarea utilizatorilor noștri. Ne străduim să oferim cele mai bune soluții digitale pentru a simplifica programările medicale, comunicarea cu medicii și accesul la informații medicale esențiale.</p>

    <h2>Viziunea Noastră</h2>
    <p>Viziunea noastră este de a deveni lideri în domeniul tehnologiei medicale, oferind o platformă completă și ușor de utilizat, care să răspundă nevoilor tuturor utilizatorilor noștri. Credem că prin inovație și dedicare, putem transforma modul în care oamenii își gestionează sănătatea.</p>

    <h2>Valorile Noastre</h2>
    <ul>
      <li><strong>Inovație:</strong> Suntem dedicați inovării continue și adoptării celor mai noi tehnologii pentru a oferi soluții eficiente și moderne.</li>
      <li><strong>Accesibilitate:</strong> Ne asigurăm că serviciile noastre sunt accesibile tuturor, indiferent de locație sau situație financiară.</li>
      <li><strong>Confidențialitate:</strong> Respectăm confidențialitatea și protecția datelor utilizatorilor noștri, conform celor mai stricte standarde.</li>
      <li><strong>Calitate:</strong> Ne angajăm să oferim servicii de cea mai înaltă calitate, cu accent pe siguranță și satisfacția utilizatorilor.</li>
      <li><strong>Empatie:</strong> Ne pasă de utilizatorii noștri și lucrăm pentru a răspunde nevoilor lor cu empatie și înțelegere.</li>
    </ul>

    <h2>Echipa Noastră</h2>
    <p>Echipa MedFM este formată din profesioniști pasionați din domeniul tehnologiei și sănătății. Cu o combinație de expertiză medicală și competențe tehnice, ne dedicăm să dezvoltăm și să menținem o platformă care să răspundă tuturor nevoilor utilizatorilor noștri.</p>

    <h2>Ce Oferim</h2>
    <ul>
      <li><strong>Programări Online:</strong> Gestionați și programați vizitele medicale direct din aplicație, rapid și eficient.</li>
      <li><strong>Comunicații Securizate:</strong> Comunicați în siguranță cu medicii dumneavoastră și primiți sfaturi medicale.</li>
      <li><strong>Istoric Medical:</strong> Accesați și gestionați istoricul medical într-un singur loc.</li>
      <li><strong>Notificări și Mementouri:</strong> Primiți notificări pentru programări și mementouri pentru a nu rata nicio vizită medicală.</li>
    </ul>

    <h2>Contact</h2>
    <p>Suntem întotdeauna aici pentru a vă ajuta. Pentru orice întrebări sau sugestii, vă rugăm să ne contactați la:</p>
    <p>E-mail: <a href="mailto:support@medfm.com">support@medfm.com</a><br>
       Telefon: +40 123 456 789</p>
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
