<?php
$servername = "localhost";
$username = "root";
$password = "39090169";
$dbname = "cursolandia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
return $conn;
?>