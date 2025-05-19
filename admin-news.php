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

// Ambil data news dari database
$query_news = mysqli_query($conn, "SELECT * FROM news ORDER BY published_at DESC");
$data_news = mysqli_fetch_all($query_news, MYSQLI_ASSOC);

// Proses penghapusan data news
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);

    // Ambil nama file gambar sebelum menghapus data
    $query_gambar = mysqli_query($conn, "SELECT gambar FROM news WHERE id = $id_hapus");
    $row_gambar = mysqli_fetch_assoc($query_gambar);
    $nama_gambar = $row_gambar['gambar'];

    $query_hapus = mysqli_query($conn, "DELETE FROM news WHERE id = $id_hapus");

    if ($query_hapus) {
        // Hapus file gambar jika ada
        if (!empty($nama_gambar) && file_exists('img/' . $nama_gambar)) {
            unlink('img/' . $nama_gambar);
        }
        echo "<script>alert('Data news berhasil dihapus!'); window.location='admin-news.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data news!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola news</title>
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
            <h2>Kelola news</h2>
            <a href="admin-tambahnews.php" class="btn-tambah">Tambah news</a>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Gambar</th>
                        <th>Penulis</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_news)): ?>
                        <tr><td colspan="7" class="text-center">Tidak ada data news.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data_news as $news): ?>
                            <tr>
                                <td><?= htmlspecialchars($news['id']) ?></td>
                                <td><?= htmlspecialchars(substr($news['title'], 0, 50)) ?><?= strlen($news['title']) > 50 ? '...' : '' ?></td>
                                <td><?= htmlspecialchars($news['author']) ?></td>
                                <td><?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($news['published_at']))) ?></td>
                                <td>
                                    <a href="#" class="aksi-btn btn-view" data-bs-toggle="modal" data-bs-target="#viewnewsModal<?= $news['id'] ?>"><i class="fa fa-eye"></i> Lihat</a>
                                    <a href="admin-editnews.php?id=<?= $news['id'] ?>" class="aksi-btn btn-edit"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="?hapus=<?= $news['id'] ?>" class="aksi-btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus news ini beserta gambarnya?')"><i class="fa fa-trash"></i> Hapus</a>
                                </td>
                            </tr>

                            <div class="modal fade" id="viewnewsModal<?= $news['id'] ?>" tabindex="-1" aria-labelledby="viewnewsModalLabel<?= $news['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewnewsModalLabel<?= $news['id'] ?>">Detail news</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php if ($news['gambar']): ?>
                                                <img src="img/<?= htmlspecialchars($news['gambar']) ?>" alt="<?= htmlspecialchars($news['title']) ?>" class="img-fluid mb-3">
                                            <?php else: ?>
                                                <p>Tidak ada foto.</p>
                                            <?php endif; ?>
                                            <p><strong>Judul:</strong> <?= htmlspecialchars($news['title']) ?></p>
                                            <p><strong>Slug:</strong> <?= htmlspecialchars($news['slug']) ?></p>
                                            <p><strong>Penulis:</strong> <?= htmlspecialchars($news['author']) ?></p>
                                            <p><strong>Tanggal Terbit:</strong> <?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($news['published_at']))) ?></p>
                                            <p><strong>Tanggal Update:</strong> <?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($news['updated_at']))) ?></p>
                                            <p><strong>Isi news:</strong><br><?= nl2br(htmlspecialchars($news['content'])) ?></p>
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