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

// Ambil data fasilitas dari database
$query_fasilitas = mysqli_query($conn, "SELECT * FROM fasilitas");
$data_fasilitas = mysqli_fetch_all($query_fasilitas, MYSQLI_ASSOC);

// Proses penghapusan data fasilitas
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);

    // Ambil nama file gambar sebelum menghapus data
    $query_gambar = mysqli_query($conn, "SELECT gambar FROM fasilitas WHERE id = $id_hapus");
    $row_gambar = mysqli_fetch_assoc($query_gambar);
    $nama_gambar = $row_gambar['gambar'];

    $query_hapus = mysqli_query($conn, "DELETE FROM fasilitas WHERE id = $id_hapus");

    if ($query_hapus) {
        // Hapus file gambar jika ada
        if (!empty($nama_gambar) && file_exists('img/' . $nama_gambar)) {
            unlink('img/' . $nama_gambar);
        }
        echo "<script>alert('Data fasilitas berhasil dihapus!'); window.location='admin-fasilitas.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data fasilitas!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Fasilitas</title>
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
        .btn-edit {
            background-color: #ffc107;
            color: black;
        }
        .btn-hapus {
            background-color: #dc3545;
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
    </style>
</head>
<body>
    <div class="content">
        <div class="table-container">
            <h2>Kelola Data Fasilitas</h2>
            <a href="admin-tambahfasilitas.php" class="btn-tambah">Tambah Fasilitas</a>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Nama Fasilitas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_fasilitas)): ?>
                        <tr><td colspan="4" class="text-center">Tidak ada data fasilitas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data_fasilitas as $fasilitas): ?>
                            <tr>
                                <td><?= htmlspecialchars($fasilitas['id']) ?></td>
                                <td>
                                    <?php if ($fasilitas['gambar']): ?>
                                        <img src="img/<?= htmlspecialchars($fasilitas['gambar']) ?>" alt="<?= htmlspecialchars($fasilitas['nama']) ?>" width="100">
                                    <?php else: ?>
                                        Tidak ada foto
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($fasilitas['nama']) ?></td>
                                <td>
                                    <a href="#" class="aksi-btn btn-view" data-bs-toggle="modal" data-bs-target="#viewFasilitasModal<?= $fasilitas['id'] ?>">Lihat</a>
                                    <a href="admin-editfasilitas.php?id=<?= $fasilitas['id'] ?>" class="aksi-btn btn-edit">Edit</a>
                                    <a href="?hapus=<?= $fasilitas['id'] ?>" class="aksi-btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini?')">Hapus</a>
                                </td>
                            </tr>

                            <div class="modal fade" id="viewFasilitasModal<?= $fasilitas['id'] ?>" tabindex="-1" aria-labelledby="viewFasilitasModalLabel<?= $fasilitas['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewFasilitasModalLabel<?= $fasilitas['id'] ?>">Detail Fasilitas</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php if ($fasilitas['gambar']): ?>
                                                <img src="img/<?= htmlspecialchars($fasilitas['gambar']) ?>" alt="<?= htmlspecialchars($fasilitas['nama']) ?>">
                                            <?php else: ?>
                                                <p>Tidak ada foto.</p>
                                            <?php endif; ?>
                                            <p><strong>Nama Fasilitas:</strong> <?= htmlspecialchars($fasilitas['nama']) ?></p>
                                            <p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($fasilitas['deskripsi'])) ?></p>
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