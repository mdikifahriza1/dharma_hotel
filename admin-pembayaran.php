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

// Ambil data pembayaran dari database
$query_pembayaran = mysqli_query($conn, "SELECT pembayaran.*, reservasi.id AS reservasi_id
                                        FROM pembayaran
                                        LEFT JOIN reservasi ON pembayaran.reservasi_id = reservasi.id
                                        ORDER BY pembayaran.tanggal_bayar DESC");
$data_pembayaran = mysqli_fetch_all($query_pembayaran, MYSQLI_ASSOC);

// Proses verifikasi pembayaran
if (isset($_GET['verifikasi']) && is_numeric($_GET['verifikasi'])) {
    $id_pembayaran = mysqli_real_escape_string($conn, $_GET['verifikasi']);

    // Dapatkan reservasi_id terkait
    $query_reservasi_id = mysqli_query($conn, "SELECT reservasi_id FROM pembayaran WHERE id = $id_pembayaran");
    $data_reservasi_id = mysqli_fetch_assoc($query_reservasi_id);
    $reservasi_id = $data_reservasi_id['reservasi_id'];

    // Update status pembayaran menjadi 'terverifikasi'
    mysqli_query($conn, "UPDATE pembayaran SET status = 'terverifikasi' WHERE id = $id_pembayaran");

    // Update status reservasi menjadi 'disetujui'
    mysqli_query($conn, "UPDATE reservasi SET status = 'disetujui' WHERE id = $reservasi_id");

    echo "<script>alert('Pembayaran berhasil diverifikasi!'); window.location='admin-pembayaran.php';</script>";
}

// Proses penolakan pembayaran
if (isset($_GET['tolak']) && is_numeric($_GET['tolak'])) {
    $id_pembayaran = mysqli_real_escape_string($conn, $_GET['tolak']);

    // Dapatkan reservasi_id terkait
    $query_reservasi_id = mysqli_query($conn, "SELECT reservasi_id FROM pembayaran WHERE id = $id_pembayaran");
    $data_reservasi_id = mysqli_fetch_assoc($query_reservasi_id);
    $reservasi_id = $data_reservasi_id['reservasi_id'];

    // Update status pembayaran menjadi 'ditolak'
    mysqli_query($conn, "UPDATE pembayaran SET status = 'ditolak' WHERE id = $id_pembayaran");

    // Update status reservasi menjadi 'dibatalkan'
    mysqli_query($conn, "UPDATE reservasi SET status = 'dibatalkan' WHERE id = $reservasi_id");

    echo "<script>alert('Pembayaran berhasil ditolak!'); window.location='admin-pembayaran.php';</script>";
}

// Proses penghapusan pembayaran
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_pembayaran = mysqli_real_escape_string($conn, $_GET['hapus']);

    // Dapatkan data pembayaran yang akan dihapus
    $query_pembayaran_data = mysqli_query($conn, "SELECT * FROM pembayaran WHERE id = $id_pembayaran");
    $data_pembayaran_data = mysqli_fetch_assoc($query_pembayaran_data);
    $foto_bukti = $data_pembayaran_data['foto_bukti'];
    $reservasi_id = $data_pembayaran_data['reservasi_id'];

    // Hapus data pembayaran
    mysqli_query($conn, "DELETE FROM pembayaran WHERE id = $id_pembayaran");

    // Hapus data reservasi terkait
    mysqli_query($conn, "DELETE FROM reservasi WHERE id = $reservasi_id");

    // Hapus foto bukti jika ada
    if (!empty($foto_bukti) && file_exists('img/' . $foto_bukti)) {
        unlink('img/' . $foto_bukti);
    }

    echo "<script>alert('Data pembayaran dan reservasi terkait berhasil dihapus!'); window.location='admin-pembayaran.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pembayaran</title>
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
        .btn-verifikasi {
            background-color: #28a745;
            color: white;
        }
        .btn-tolak {
            background-color: #dc3545;
            color: white;
        }
        .btn-hapus {
            background-color: #6c757d;
            color: white;
        }
        .modal-img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="table-container">
            <h2>Kelola Pembayaran</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Pembayaran</th>
                        <th>ID Reservasi</th>
                        <th>Foto Bukti</th>
                        <th>Tanggal Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_pembayaran)): ?>
                        <tr><td colspan="6" class="text-center">Tidak ada data pembayaran.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data_pembayaran as $pembayaran): ?>
                            <tr>
                                <td><?= htmlspecialchars($pembayaran['id']) ?></td>
                                <td><?= htmlspecialchars($pembayaran['reservasi_id']) ?></td>
                                <td>
                                    <?php if ($pembayaran['foto_bukti']): ?>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalBukti<?= $pembayaran['id'] ?>">
                                            Lihat Bukti
                                        </a>
                                    <?php else: ?>
                                        Tidak ada bukti
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($pembayaran['tanggal_bayar']) ?></td>
                                <td><?= htmlspecialchars($pembayaran['status']) ?></td>
                                <td>
                                    <?php if ($pembayaran['foto_bukti'] && $pembayaran['status'] != 'terverifikasi'): ?>
                                        <a href="?verifikasi=<?= $pembayaran['id'] ?>" class="aksi-btn btn-verifikasi">Verifikasi</a>
                                        <a href="?tolak=<?= $pembayaran['id'] ?>" class="aksi-btn btn-tolak">Tolak</a>
                                    <?php elseif ($pembayaran['status'] != 'terverifikasi' && $pembayaran['status'] != 'ditolak'): ?>
                                        <a href="?tolak=<?= $pembayaran['id'] ?>" class="aksi-btn btn-tolak">Tolak</a>
                                    <?php elseif ($pembayaran['status'] == 'ditolak' && $pembayaran['foto_bukti'] != null): ?>
                                        <a href="?hapus=<?= $pembayaran['id'] ?>" class="aksi-btn btn-hapus">Hapus</a>
                                    <?php elseif ($pembayaran['status'] == 'ditolak' && $pembayaran['foto_bukti'] == null): ?>
                                        <a href="?hapus=<?= $pembayaran['id'] ?>" class="aksi-btn btn-hapus">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <div class="modal fade" id="modalBukti<?= $pembayaran['id'] ?>" tabindex="-1" aria-labelledby="modalBuktiLabel<?= $pembayaran['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalBuktiLabel<?= $pembayaran['id'] ?>">Bukti Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="img/pembayaran/<?= htmlspecialchars($pembayaran['foto_bukti']) ?>" class="modal-img">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
