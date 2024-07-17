<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

if (!isset($_SESSION['webminar_id'])) {
    die("Webminar ID not set in session.");
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
require_once 'functions.php';

$webminar_id = $_SESSION['webminar_id'];

// Colectarea adreselor de email pentru webminar_id-ul curent
$stmt = $conn->prepare("SELECT email FROM inscrieri_webminarii WHERE webminar_id = ?");
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param('i', $webminar_id);
if (!$stmt->execute()) {
    die("Execute statement failed: " . $stmt->error);
}
$result = $stmt->get_result();
$emails = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Collect Emails</title>
  <style>
    .email-list {
      margin: 20px;
      padding: 20px;
      border: 1px solid #ccc;
    }
    .email-item {
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="email-list">
    <h2>Email List for Webminar ID: <?php echo htmlspecialchars($webminar_id); ?></h2>
    <?php if (count($emails) > 0): ?>
        <ul>
            <?php foreach ($emails as $email): ?>
                <li class="email-item"><?php echo htmlspecialchars($email['email']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No emails found for this webminar.</p>
    <?php endif; ?>
  </div>
</body>
</html>
