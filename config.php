<?php 
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = "localhost";
$username = "root"; 
$password = "oreoluwa2003"; 
$dbname = "ILMS"; 
    
// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);
    
// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>