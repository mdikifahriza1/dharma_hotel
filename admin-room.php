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

// Ambil data kamar dari database
$query_kamar = mysqli_query($conn, "SELECT * FROM room ORDER BY nama_kamar ASC");
$data_kamar = mysqli_fetch_all($query_kamar, MYSQLI_ASSOC);

// Proses penghapusan data kamar
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);

    // Ambil nama file gambar sebelum menghapus data
    $query_gambar = mysqli_query($conn, "SELECT gambar FROM room WHERE id_kamar = $id_hapus");
    $row_gambar = mysqli_fetch_assoc($query_gambar);
    $nama_gambar = $row_gambar['gambar'];

    // Hapus data reservasi terkait
    mysqli_query($conn, "DELETE FROM reservasi WHERE room_id = $id_hapus");

    // Ambil ID reservasi yang terhapus untuk menghapus ulasan terkait
    $query_reservasi_terhapus = mysqli_query($conn, "SELECT id FROM reservasi WHERE room_id = $id_hapus");
    while ($row_reservasi_terhapus = mysqli_fetch_assoc($query_reservasi_terhapus)) {
        $reservasi_id_terhapus = $row_reservasi_terhapus['id'];
        // Hapus ulasan terkait dengan reservasi yang terhapus
        mysqli_query($conn, "DELETE FROM ulasan WHERE reservasi_id = $reservasi_id_terhapus");
        mysqli_query($conn, "DELETE FROM pembayaran WHERE reservasi_id = $reservasi_id_terhapus");
    }

    // Hapus data kamar
    $query_hapus_kamar = mysqli_query($conn, "DELETE FROM room WHERE id_kamar = $id_hapus");

    if ($query_hapus_kamar) {
        // Hapus file gambar jika ada
        if (!empty($nama_gambar) && file_exists('img/' . $nama_gambar)) {
            unlink('img/' . $nama_gambar);
        }
        echo "<script>alert('Data kamar dan data terkait berhasil dihapus!'); window.location='admin-room.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data kamar!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kamar</title>
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
        .btn-view {
            background-color: #007bff;
            color: white;
        }
        .btn-tambah {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn-tambah:hover {
            background-color: #218838;
        }
        .btn-edit {
            background-color: #ffc107;
            color: black;
        }
        .btn-hapus {
            background-color: #dc3545;
            color: white;
        }
        .modal-dialog {
            max-width: 800px;
        }
        .modal-body img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .modal-body p {
            margin-bottom: 10px;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="table-container">
            <h2>Kelola Kamar</h2>
            <a href="admin-tambahroom.php" class="btn-tambah">Tambah Kamar</a>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kamar</th>
                        <th>Gambar</th>
                        <th>Ketersediaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_kamar)): ?>
                        <tr><td colspan="5" class="text-center">Tidak ada data kamar.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data_kamar as $kamar): ?>
                            <tr>
                                <td><?= htmlspecialchars($kamar['id_kamar']) ?></td>
                                <td><?= htmlspecialchars($kamar['nama_kamar']) ?></td>
                                <td>
                                    <?php if ($kamar['gambar']): ?>
                                        <img src="img/<?= htmlspecialchars($kamar['gambar']) ?>" alt="<?= htmlspecialchars($kamar['nama_kamar']) ?>" width="100">
                                    <?php else: ?>
                                        Tidak ada foto
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $kamar['ketersediaan'] ?>
                                </td>
                                <td>
                                    <a href="#" class="aksi-btn btn-view" data-bs-toggle="modal" data-bs-target="#viewKamarModal<?= $kamar['id_kamar'] ?>"><i class="fa fa-eye"></i> Lihat</a>
                                    <a href="admin-editroom.php?id=<?= $kamar['id_kamar'] ?>" class="aksi-btn btn-edit"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="?hapus=<?= $kamar['id_kamar'] ?>" class="aksi-btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus kamar ini beserta data reservasi dan ulasan terkait?')"><i class="fa fa-trash"></i> Hapus</a>
                                </td>
                            </tr>

                            <div class="modal fade" id="viewKamarModal<?= $kamar['id_kamar'] ?>" tabindex="-1" aria-labelledby="viewKamarModalLabel<?= $kamar['id_kamar'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewKamarModalLabel<?= $kamar['id_kamar'] ?>">Detail Kamar</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php if ($kamar['gambar']): ?>
                                                <img src="img/<?= htmlspecialchars($kamar['gambar']) ?>" alt="<?= htmlspecialchars($kamar['nama_kamar']) ?>" class="img-fluid mb-3">
                                            <?php else: ?>
                                                <p>Tidak ada foto.</p>
                                            <?php endif; ?>
                                            <p><strong>ID Kamar:</strong> <?= htmlspecialchars($kamar['id_kamar']) ?></p>
                                            <p><strong>Nama Kamar:</strong> <?= htmlspecialchars($kamar['nama_kamar']) ?></p>
                                            <p><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($kamar['deskripsi'])) ?></p>
                                            <p><strong>Harga:</strong> Rp <?= number_format($kamar['harga'], 0, ',', '.') ?></p>
                                            <p><strong>Ketersediaan:</strong> <?php echo $kamar['ketersediaan'] ?></p>
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