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

// Ambil ID fasilitas dari parameter GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID fasilitas tidak valid!'); window.location='admin-fasilitas.php';</script>";
    exit;
}
$id_fasilitas = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data fasilitas berdasarkan ID
$query_get_fasilitas = mysqli_query($conn, "SELECT * FROM fasilitas WHERE id = $id_fasilitas");
$data_fasilitas = mysqli_fetch_assoc($query_get_fasilitas);

// Jika data fasilitas tidak ditemukan
if (!$data_fasilitas) {
    echo "<script>alert('Data fasilitas tidak ditemukan!'); window.location='admin-fasilitas.php';</script>";
    exit;
}

$upload_error = '';
$upload_success = '';

if (isset($_POST['edit_fasilitas'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $nama_gambar_baru = $data_fasilitas['gambar']; // Default ke gambar lama

    if (isset($_FILES['gambar_baru']) && $_FILES['gambar_baru']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $file_name = $_FILES['gambar_baru']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_tmp = $_FILES['gambar_baru']['tmp_name'];
        $file_size = $_FILES['gambar_baru']['size'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file_ext, $allowed_types)) {
            $upload_error = 'Format file tidak diizinkan. Hanya JPG, JPEG, dan PNG yang diperbolehkan.';
        } elseif ($file_size > $max_size) {
            $upload_error = 'Ukuran file terlalu besar. Maksimal 2MB.';
        } else {
            $new_file_name = uniqid('fasilitas_') . '.' . $file_ext;
            $upload_path = 'img/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Hapus file lama jika bukan default dan ada
                if (!empty($data_fasilitas['gambar']) && file_exists('img/' . $data_fasilitas['gambar'])) {
                    unlink('img/' . $data_fasilitas['gambar']);
                }
                $nama_gambar_baru = $new_file_name;
            } else {
                $upload_error = 'Terjadi kesalahan saat mengupload file.';
            }
        }
    }

    if (empty($upload_error)) {
        $query_update = mysqli_query($conn, "UPDATE fasilitas SET nama = '$nama', deskripsi = '$deskripsi', gambar = '$nama_gambar_baru' WHERE id = $id_fasilitas");
        if ($query_update) {
            echo "<script>alert('Data fasilitas berhasil diperbarui!'); window.location='admin-fasilitas.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat memperbarui data fasilitas!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Fasilitas</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }
        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        .current-image {
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
        }
        .current-image img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .btn-simpan {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-simpan:hover {
            background-color: #218838;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="form-container">
            <h2>Edit Fasilitas</h2>
            <?php if ($upload_error): ?>
                <div class="alert alert-danger"><?= $upload_error ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama">Nama Fasilitas:</label>
                    <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data_fasilitas['nama']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi:</label>
                    <textarea id="deskripsi" name="deskripsi"><?= htmlspecialchars($data_fasilitas['deskripsi']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Foto Saat Ini:</label>
                    <div class="current-image">
                        <?php if ($data_fasilitas['gambar']): ?>
                            <img src="img/<?= htmlspecialchars($data_fasilitas['gambar']) ?>" alt="<?= htmlspecialchars($data_fasilitas['nama']) ?>">
                        <?php else: ?>
                            Tidak ada foto saat ini.
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gambar_baru">Ganti Foto Fasilitas (opsional):</label>
                    <input type="file" id="gambar_baru" name="gambar_baru" accept=".jpg,.jpeg,.png">
                    <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG. Ukuran maksimal: 2MB. Jika tidak ingin mengganti foto, biarkan kosong.</small>
                </div>
                <button type="submit" class="btn-simpan" name="edit_fasilitas">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>