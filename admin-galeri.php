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

// Ambil data galeri dari database
$query_galeri = mysqli_query($conn, "SELECT * FROM galeri ORDER BY tanggal DESC");
$data_galeri = mysqli_fetch_all($query_galeri, MYSQLI_ASSOC);

// Proses penghapusan data galeri
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);

    // Ambil nama file gambar untuk dihapus
    $query_gambar = mysqli_query($conn, "SELECT gambar FROM galeri WHERE id = $id_hapus");
    $data_gambar = mysqli_fetch_assoc($query_gambar);
    $nama_gambar = $data_gambar['gambar'];

    // Hapus data galeri
    $query_hapus_galeri = mysqli_query($conn, "DELETE FROM galeri WHERE id = $id_hapus");

    if ($query_hapus_galeri) {
        // Hapus file gambar jika ada
        if (!empty($nama_gambar) && file_exists('img/' . $nama_gambar)) {
            unlink('img/' . $nama_gambar);
        }
        echo "<script>alert('Data galeri berhasil dihapus!'); window.location='admin-galeri.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data galeri!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Galeri</title>
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
        .btn-lihat {
            background-color: #17a2b8;
            color: white;
        }
        .btn-edit {
            background-color: #28a745;
            color: white;
        }
        .btn-hapus {
            background-color: #dc3545;
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
            <h2>Kelola Galeri</h2>
            <a href="admin-tambahgaleri.php" class="btn btn-primary mb-3">Tambah Foto</a>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kategori</th>
                        <th>Judul</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_galeri)): ?>
                        <tr><td colspan="7" class="text-center">Tidak ada data galeri.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data_galeri as $galeri): ?>
                            <tr>
                                <td><?= htmlspecialchars($galeri['id']) ?></td>
                                <td><?= htmlspecialchars($galeri['kategori']) ?></td>
                                <td><?= htmlspecialchars($galeri['judul']) ?></td>
                                <td>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalGambar<?= $galeri['id'] ?>">
                                        Lihat Gambar
                                    </a>
                                </td>
                                <td>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $galeri['id'] ?>" class="aksi-btn btn-lihat"><i class="fa fa-eye"></i> Lihat</a>
                                    <a href="admin-editgaleri.php?id=<?= $galeri['id'] ?>" class="aksi-btn btn-edit"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="?hapus=<?= $galeri['id'] ?>" class="aksi-btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus foto ini?')"><i class="fa fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                            <div class="modal fade" id="modalDetail<?= $galeri['id'] ?>" tabindex="-1" aria-labelledby="modalDetailLabel<?= $galeri['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalDetailLabel<?= $galeri['id'] ?>">Detail Gambar</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <img src="img/<?= htmlspecialchars($galeri['gambar']) ?>" class="modal-img">
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>ID:</strong> <?= htmlspecialchars($galeri['id']) ?></p>
                                                    <p><strong>Kategori:</strong> <?= htmlspecialchars($galeri['kategori']) ?></p>
                                                    <p><strong>Judul:</strong> <?= htmlspecialchars($galeri['judul']) ?></p>
                                                    <p><strong>Deskripsi:</strong> <?= htmlspecialchars($galeri['deskripsi']) ?></p>
                                                    <p><strong>Tanggal:</strong> <?= htmlspecialchars($galeri['tanggal']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="modalGambar<?= $galeri['id'] ?>" tabindex="-1" aria-labelledby="modalGambarLabel<?= $galeri['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalGambarLabel<?= $galeri['id'] ?>">Gambar</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="img/<?= htmlspecialchars($galeri['gambar']) ?>" class="modal-img">
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
