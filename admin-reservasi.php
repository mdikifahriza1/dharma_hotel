<?php
include 'koneksi.php';
include 'sidebar.php';

session_start();
$admin_id = $_SESSION['id'] ?? null;

if (!$admin_id) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE id = $admin_id AND role = 'admin'");
$admin = mysqli_fetch_assoc($query_admin);

if (!$admin) {
    echo "<script>alert('Data admin tidak ditemukan!'); window.location='logout.php';</script>";
    exit;
}

// Ambil data reservasi dari database
$query_reservasi = mysqli_query($conn, "SELECT reservasi.*, users.full_name, room.nama_kamar 
                                        FROM reservasi 
                                        JOIN users ON reservasi.user_id = users.id 
                                        JOIN room ON reservasi.room_id = room.id_kamar
                                        ORDER BY reservasi.check_in DESC");
$data_reservasi = mysqli_fetch_all($query_reservasi, MYSQLI_ASSOC);

// Proses penghapusan data reservasi
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);

    // Hapus data pembayaran terkait
    mysqli_query($conn, "DELETE FROM pembayaran WHERE reservasi_id = $id_hapus");

    // Hapus data reservasi
    $query_hapus_reservasi = mysqli_query($conn, "DELETE FROM reservasi WHERE id = $id_hapus");

    if ($query_hapus_reservasi) {
        echo "<script>alert('Data reservasi dan pembayaran terkait berhasil dihapus!'); window.location='admin-reservasi.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data reservasi!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Reservasi</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .table-container {
            max-width: 95%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .aksi-btn {
            margin-right: 5px;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-hapus {
            background-color: #dc3545;
            color: white;
        }
        .btn-hapus:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="table-container">
            <h2>Kelola Reservasi</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Reservasi</th>
                        <th>Nama Pemesan</th>
                        <th>Nama Kamar</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Jumlah Tamu</th>
                        <th>Status</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_reservasi)): ?>
                        <tr><td colspan="9" class="text-center">Tidak ada data reservasi.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data_reservasi as $reservasi): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservasi['id']) ?></td>
                                <td><?= htmlspecialchars($reservasi['full_name']) ?></td>
                                <td><?= htmlspecialchars($reservasi['nama_kamar']) ?></td>
                                <td><?= htmlspecialchars($reservasi['check_in']) ?></td>
                                <td><?= htmlspecialchars($reservasi['check_out']) ?></td>
                                <td><?= htmlspecialchars($reservasi['jumlah_tamu']) ?></td>
                                <td><?= htmlspecialchars($reservasi['status']) ?></td>
                                <td>Rp <?= number_format($reservasi['total_harga'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="?hapus=<?= $reservasi['id'] ?>" class="aksi-btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus reservasi ini beserta pembayaran terkait?')"><i class="fa fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
