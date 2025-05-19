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

$error = '';
$nama_kamar = '';
$deskripsi = '';
$harga = '';
$ketersediaan = '';
$gambar = '';

if (isset($_POST['tambah_kamar'])) {
    $nama_kamar = mysqli_real_escape_string($conn, $_POST['nama_kamar']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $ketersediaan = mysqli_real_escape_string($conn, $_POST['ketersediaan']); //ambil dari input number
    

    // Validasi input
    if (empty($nama_kamar)) {
        $error = "Nama kamar harus diisi.";
    } elseif (empty($deskripsi)) {
        $error = "Deskripsi harus diisi.";
    } elseif (empty($harga) || !is_numeric($harga) || $harga <= 0) {
        $error = "Harga harus diisi dengan angka yang valid.";
    } elseif (empty($ketersediaan) || !is_numeric($ketersediaan) || $ketersediaan < 0) { //validasi ketersediaan
        $error = "Ketersediaan harus diisi dengan angka yang valid dan tidak negatif.";
    }

    // Proses upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file_name = $_FILES['gambar']['name'];
        $file_size = $_FILES['gambar']['size'];
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_type = $_FILES['gambar']['type'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file_type, $allowed_types)) {
            $error = "Jenis file tidak diizinkan. Hanya JPG, PNG, dan GIF yang diperbolehkan.";
        } elseif ($file_size > $max_size) {
            $error = "Ukuran file terlalu besar. Maksimal 2MB.";
        } else {
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = 'kamar_' . time() . '.' . $file_ext; // Nama unik
            $upload_path = 'img/' . $new_file_name;

            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $error = "Gagal mengupload gambar.";
            } else {
                $gambar = $new_file_name; // Simpan nama file untuk disimpan di database
            }
        }
    } else {
        $gambar = ''; // Set gambar ke kosong jika tidak ada file yang diupload
    }

    if (empty($error)) {
        // Simpan data kamar ke database
        $query_insert_kamar = mysqli_query($conn, "INSERT INTO room (nama_kamar, deskripsi, harga, ketersediaan, gambar) 
                                                    VALUES ('$nama_kamar', '$deskripsi', '$harga', '$ketersediaan', '$gambar')");

        if ($query_insert_kamar) {
            echo "<script>alert('Data kamar berhasil ditambahkan!'); window.location='admin-room.php';</script>";
            exit;
        } else {
            $error = "Terjadi kesalahan saat menambahkan data kamar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kamar</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        textarea,
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        .form-check-label {
            margin-left: 5px;
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
    </style>
</head>
<body>
    <div class="content">
        <div class="form-container">
            <h2>Tambah Kamar</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama_kamar">Nama Kamar:</label>
                    <input type="text" id="nama_kamar" name="nama_kamar" value="<?= htmlspecialchars($nama_kamar) ?>" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi:</label>
                    <textarea id="deskripsi" name="deskripsi" required><?= htmlspecialchars($deskripsi) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="harga">Harga:</label>
                    <input type="number" id="harga" name="harga" value="<?= htmlspecialchars($harga) ?>" required>
                </div>
                <div class="form-group">
                    <label for="ketersediaan">Ketersediaan:</label>
                    <input type="number" id="ketersediaan" name="ketersediaan" value="<?= htmlspecialchars($ketersediaan) ?>" required>
                </div>
                <div class="form-group">
                    <label for="gambar">Gambar (Max 2MB, JPG, PNG, GIF):</label>
                    <input type="file" id="gambar" name="gambar">
                </div>
                <button type="submit" class="btn-simpan" name="tambah_kamar">Tambah Kamar</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
