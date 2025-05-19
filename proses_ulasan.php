<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pastikan ada id user
    if (!isset($_SESSION['id'])) {
        echo "Anda harus login terlebih dahulu.";
        exit;
    }
    $user_id = $_SESSION['id'];

    // Validasi data yang dikirimkan
    if (!isset($_POST['reservasi_id'], $_POST['isi_ulasan_' . $_POST['reservasi_id']], $_POST['rating_' . $_POST['reservasi_id']])) {
        echo "Data tidak lengkap.";
        exit;
    }

    // Menangkap input dari form
    $reservasi_id = $_POST['reservasi_id'];
    $isi_ulasan = mysqli_real_escape_string($conn, $_POST['isi_ulasan_' . $reservasi_id]);
    $rating = (int) $_POST['rating_' . $reservasi_id];
    $tanggal_ulasan = date('Y-m-d H:i:s');

    // Validasi rating
    if ($rating < 1 || $rating > 5) {
        echo "Rating tidak valid.";
        exit;
    }

    // Cek apakah ulasan sudah ada
    $check_query = "SELECT * FROM ulasan WHERE reservasi_id = $reservasi_id AND user_id = $user_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "Anda sudah memberikan ulasan untuk reservasi ini.";
        exit;
    }

    // Menambahkan ulasan baru
    $insert_query = "INSERT INTO ulasan (user_id, reservasi_id, isi_ulasan, rating, tanggal) 
                     VALUES ('$user_id', '$reservasi_id', '$isi_ulasan', '$rating', '$tanggal_ulasan')";

    if (mysqli_query($conn, $insert_query)) {
        echo "Ulasan berhasil ditambahkan.";
        // Redirect ke halaman ulasan.php setelah berhasil menambah ulasan
        header("Location: ulasan.php");
        exit;
    } else {
        echo "Gagal menambahkan ulasan: " . mysqli_error($conn);
    }
}
?>
