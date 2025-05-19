<?php
include 'koneksi.php';
include 'sidebar.php';

session_start();

// Pastikan hanya admin yang bisa mengakses
$id = $_SESSION['id'] ?? null;
if (!$id) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

// Ambil data dari tabel about (diasumsikan hanya 1 baris)
$about = mysqli_query($conn, "SELECT * FROM about LIMIT 1");
$data = mysqli_fetch_assoc($about);

// Update data jika form disubmit melalui modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_about'])) {
    $tentangkami_beranda = mysqli_real_escape_string($conn, $_POST['tentangkami_beranda']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $facebook = mysqli_real_escape_string($conn, $_POST['facebook']);
    $instagram = mysqli_real_escape_string($conn, $_POST['instagram']);
    $youtube = mysqli_real_escape_string($conn, $_POST['youtube']);

    // Jika data sudah ada, update. Jika belum, insert.
    if ($data) {
        $id = $data['id'];
        $update = mysqli_query($conn, "UPDATE about SET
            tentangkami_beranda = '$tentangkami_beranda',
            deskripsi = '$deskripsi',
            alamat = '$alamat',
            facebook = '$facebook',
            instagram = '$instagram',
            youtube = '$youtube'
            WHERE id = $id
        ");
    } else {
        $update = mysqli_query($conn, "INSERT INTO about (
            tentangkami_beranda, deskripsi, alamat, facebook, instagram, youtube
        ) VALUES (
            '$tentangkami_beranda', '$deskripsi', '$alamat', '$facebook', '$instagram', '$youtube'
        )");
    }

    if ($update) {
        echo "<script>alert('Data About berhasil disimpan!'); window.location='admin-about.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menyimpan!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Halaman Tentang Kami</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .card {
            max-width: 800px;
            margin: 40px auto;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f0f0f0;
            padding: 15px;
            border-bottom: 1px solid #ccc;
            border-radius-top-left: 8px;
            border-radius-top-right: 8px;
            text-align: center;
        }

        .card-body {
            padding: 20px;
        }

        .card-text {
            margin-bottom: 10px;
        }

        .modal-dialog {
            max-width: 800px;
        }

        .modal-body .form-group {
            margin-bottom: 15px;
        }

        .modal-body label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .modal-body input[type="text"],
        .modal-body textarea {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .modal-body textarea {
            resize: vertical;
            min-height: 100px;
        }
    </style>
</head>
<body>
<div class="content">
    <div class="card">
        <div class="card-header">
            <h2>Halaman Tentang Kami</h2>
        </div>
        <div class="card-body">
            <?php if ($data): ?>
                <p class="card-text"><strong>Tentang Kami (Beranda):</strong><br><?= nl2br(htmlspecialchars($data['tentangkami_beranda'])) ?></p>
                <p class="card-text"><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>
                <p class="card-text"><strong>Alamat:</strong> <?= htmlspecialchars($data['alamat']) ?></p>
                <p class="card-text"><strong>Facebook:</strong> <?= htmlspecialchars($data['facebook']) ?></p>
                <p class="card-text"><strong>Instagram:</strong> <?= htmlspecialchars($data['instagram']) ?></p>
                <p class="card-text"><strong>YouTube:</strong> <?= htmlspecialchars($data['youtube']) ?></p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editAboutModal">
                    Edit
                </button>
            <?php else: ?>
                <p class="card-text">Belum ada data Tentang Kami.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editAboutModal">
                    Tambah Data
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="editAboutModal" tabindex="-1" aria-labelledby="editAboutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAboutModalLabel">Edit Halaman Tentang Kami</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="tentangkami_beranda_modal">Tentang Kami (Beranda)</label>
                            <textarea class="form-control" id="tentangkami_beranda_modal" name="tentangkami_beranda"><?= htmlspecialchars($data['tentangkami_beranda'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi_modal">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi_modal" name="deskripsi"><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="alamat_modal">Alamat</label>
                            <input type="text" class="form-control" id="alamat_modal" name="alamat" value="<?= htmlspecialchars($data['alamat'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="facebook_modal">Facebook</label>
                            <input type="text" class="form-control" id="facebook_modal" name="facebook" value="<?= htmlspecialchars($data['facebook'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="instagram_modal">Instagram</label>
                            <input type="text" class="form-control" id="instagram_modal" name="instagram" value="<?= htmlspecialchars($data['instagram'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="youtube_modal">YouTube</label>
                            <input type="text" class="form-control" id="youtube_modal" name="youtube" value="<?= htmlspecialchars($data['youtube'] ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_about">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>