<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto_bayar'])) {
    $foto_bayar = $_FILES['foto_bayar'];
    $id_reservasi = $_POST['reservasi_id'];

    // Lokasi penyimpanan
    $target_dir = "img/pembayaran/";
    $nama_file_unik = uniqid('bukti_') . "-" . basename($foto_bayar["name"]);
    $target_file = $target_dir . $nama_file_unik;

    // Coba upload file
    if (move_uploaded_file($foto_bayar["tmp_name"], $target_file)) {
        // Update ke tabel pembayaran, kolom foto_bukti
        $query = "UPDATE pembayaran SET foto_bukti = '$nama_file_unik' WHERE reservasi_id = $id_reservasi";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Bukti pembayaran berhasil di-upload!');
                    window.location.href = 'tagihan.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal mengupdate bukti pembayaran: " . mysqli_error($conn) . "');
                    window.location.href = 'tagihan.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Terjadi kesalahan saat mengupload file.');
                window.location.href = 'tagihan.php';
              </script>";
    }
}
?>
