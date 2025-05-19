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

// Bagian 1: Reservasi yang Belum Diulas
$query_reservasi_belum_ulas = mysqli_query($conn, "
    SELECT r.id, r.check_in, r.check_out, r.status, rm.nama_kamar, r.total_harga, us.full_name, r.user_id
    FROM reservasi r
    JOIN room rm ON r.room_id = rm.id_kamar
    JOIN users us ON r.user_id = us.id
    WHERE r.status = 'disetujui' 
    AND NOT EXISTS (
        SELECT 1
        FROM ulasan u
        WHERE u.reservasi_id = r.id
    )
");
$data_reservasi_belum_ulas = mysqli_fetch_all($query_reservasi_belum_ulas, MYSQLI_ASSOC);

// Bagian 2: Data Ulasan yang Sudah Ada
$query_ulasan = mysqli_query($conn, "
    SELECT
        u.id AS id_ulasan,
        us.full_name AS nama_lengkap,
        u.isi_ulasan,
        u.rating,
        u.tanggal,
        r.id AS id_reservasi
    FROM ulasan u
    JOIN users us ON u.user_id = us.id
    JOIN reservasi r ON u.reservasi_id = r.id
    ORDER BY u.tanggal DESC
");
$data_ulasan = mysqli_fetch_all($query_ulasan, MYSQLI_ASSOC);

// Proses penghapusan data ulasan
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    $query_hapus = mysqli_query($conn, "DELETE FROM ulasan WHERE id = $id_hapus");
    if ($query_hapus) {
        echo "<script>alert('Data ulasan berhasil dihapus!'); window.location='admin-ulasan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data ulasan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Ulasan</title>
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
        .btn-edit {
            background-color: #ffc107;
            color: black;
        }
        .btn-hapus {
            background-color: #dc3545;
            color: white;
        }
        .btn-ulas {
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
        }
        .btn-ulas:hover {
            background-color: #218838;
            color: white;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="table-container">
            <h2>Reservasi yang Belum Diulas</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Reservasi</th>
                        <th>Nama Pemesan</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Nama Kamar</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_reservasi_belum_ulas)): ?>
                        <tr><td colspan="8" class="text-center">Tidak ada data reservasi yang belum diulas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data_reservasi_belum_ulas as $reservasi): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservasi['id']) ?></td>
                                <td><?= htmlspecialchars($reservasi['full_name']) ?></td>
                                <td><?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($reservasi['check_in']))) ?></td>
                                <td><?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($reservasi['check_out']))) ?></td>
                                <td><?= htmlspecialchars($reservasi['status']) ?></td>
                                <td><?= htmlspecialchars($reservasi['nama_kamar']) ?></td>
                                <td><?= htmlspecialchars($reservasi['total_harga']) ?></td>
                                <td>
                                    <a href="admin-tambahulasan.php?reservasi_id=<?= $reservasi['id'] ?>&user_id=<?= $reservasi['user_id'] ?>" class="btn-ulas">Ulas</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <h2>Data Ulasan</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Ulasan</th>
                        <th>Nama Pengulas</th>
                        <th>Isi Ulasan</th>
                        <th>Rating</th>
                        <th>tanggal</th>
                        <th>ID Reservasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_ulasan)): ?>
                        <tr><td colspan="7" class="text-center">Tidak ada data ulasan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data_ulasan as $ulasan): ?>
                            <tr>
                                <td><?= htmlspecialchars($ulasan['id_ulasan']) ?></td>
                                <td><?= htmlspecialchars($ulasan['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($ulasan['isi_ulasan']) ?></td>
                                <td><?= htmlspecialchars($ulasan['rating']) ?></td>
                                <td><?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($ulasan['tanggal']))) ?></td>
                                <td><?= htmlspecialchars($ulasan['id_reservasi']) ?></td>
                                <td>
                                    <a href="admin-editulasan.php?id=<?= $ulasan['id_ulasan'] ?>" class="aksi-btn btn-edit">Edit</a>
                                    <a href="?hapus=<?= $ulasan['id_ulasan'] ?>" class="aksi-btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?')">Hapus</a>
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
