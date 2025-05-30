    <?php
$server   = "localhost";
$username = "root";
$password = "";
$database = "dharma_hotel";

$conn = mysqli_connect($server, $username, $password, $database);

// cek koneksi
if (!$conn) {
    die('Koneksi Database Gagal : ' . mysqli_connect_error());
}
?>