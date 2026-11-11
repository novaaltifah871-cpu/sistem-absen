
<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "sekolah_db";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mengatur charset database
mysqli_set_charset($conn, "utf8mb4");

?>
