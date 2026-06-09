<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mboCinemas";

try {
  $conn = new PDO(
    "mysql:host=$servername;dbname=$dbname", 
    $username, 
    $password
  );
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // echo "Connected successfully";
  $DBerr = false;
} catch(PDOException $e) {
  $DBerr = true;
}
?>