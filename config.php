<?php
$servername = "localhost";
$username = "root"; // MySQL username
$password = "Parola!noua12345"; // MySQL password
$dbname = "cabinete_medicale"; // MySQL database name
$port = 3308; // MySQL port

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>