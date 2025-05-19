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

// Ambil data pengguna
$users = mysqli_query($conn, "SELECT * FROM users");

// Hapus pengguna jika tombol hapus ditekan (selain admin yang login)
if (isset($_GET['delete_id']) && $_GET['delete_id'] != $admin_id) {
    $delete_id = $_GET['delete_id'];

    // Ambil data pengguna yang akan dihapus untuk mendapatkan nama file foto
    $query_user = mysqli_query($conn, "SELECT foto FROM users WHERE id = $delete_id");
    $user_data = mysqli_fetch_assoc($query_user);
    $old_foto = $user_data['foto'];

    // Hapus data terkait pengguna di tabel lain
    // 1. Hapus reservasi terkait
    $query_reservasi = mysqli_query($conn, "SELECT id FROM reservasi WHERE user_id = $delete_id");
    while ($row_reservasi = mysqli_fetch_assoc($query_reservasi)) {
        $reservasi_id = $row_reservasi['id'];

        // 2. Hapus pembayaran terkait dengan reservasi yang dihapus
        mysqli_query($conn, "DELETE FROM pembayaran WHERE reservasi_id = $reservasi_id");

        // 3. Hapus ulasan terkait dengan reservasi yang dihapus
        mysqli_query($conn, "DELETE FROM ulasan WHERE reservasi_id = $reservasi_id");
    }
    mysqli_query($conn, "DELETE FROM reservasi WHERE user_id = $delete_id");

    // 4. Hapus ulasan yang langsung terkait dengan user_id
    mysqli_query($conn, "DELETE FROM ulasan WHERE user_id = $delete_id");

    // Hapus data pengguna
    mysqli_query($conn, "DELETE FROM users WHERE id = $delete_id");

    // Hapus file foto lama jika ada
    if (!empty($old_foto) && file_exists('img/' . $old_foto)) {
        unlink('img/' . $old_foto);
    }

    echo "<script>alert('Pengguna berhasil dihapus!'); window.location='admin-users.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Profil Pengguna</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .btn {
            padding: 8px 16px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
        }

        .btn-danger {
            background: #e53e3e;
        }

        .btn:hover {
            background-color: #1d4ed8;
        }

        .btn-danger:hover {
            background-color: #9b2c2c;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 60px;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
            position: relative;
        }

        .close {
            color: #aaa;
            position: absolute;
            top: 10px; right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        img {
            max-width: 100px;
            margin: 10px 0;
        }

        .modal input, .modal select, .modal textarea {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
        }

        .sidebar-toggle {
            display: none;
        }
    </style>
</head>
<body>
<div class="content">
    <h1>Manajemen Profil Pengguna</h1>
    <a href="admin-tambahusers.php" class="btn">Tambah Pengguna</a>
    <h1></h1>
    <table>
        <tr>
            <th>Foto</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
        <?php while ($u = mysqli_fetch_assoc($users)): ?>
        <tr>
            <td><img src="img/<?= $u['foto'] ?>" alt="Foto Pengguna"></td>
            <td><?= $u['full_name'] ?></td>
            <td><?= $u['email'] ?></td>
            <td><?= $u['role'] ?></td>
            <td>
                <button class="btn" onclick="openModal(<?= htmlspecialchars(json_encode($u)) ?>)">View</button>
                <a href="admin-editusers.php?id=<?= $u['id'] ?>" class="btn">Edit</a>
                <?php if ($u['id'] != $admin_id): ?>
                    <a href="?delete_id=<?= $u['id'] ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- MODAL -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Detail Pengguna</h2>
        <div>
            <img id="view-foto" src="" alt="Foto Pengguna">
            <p><strong>Username:</strong> <span id="view-username"></span></p>
            <p><strong>Email:</strong> <span id="view-email"></span></p>
            <p><strong>Nama Lengkap:</strong> <span id="view-fullname"></span></p>
            <p><strong>NIK:</strong> <span id="view-nik"></span></p>
            <p><strong>Tanggal Lahir:</strong> <span id="view-tanggal"></span></p>
            <p><strong>Jenis Kelamin:</strong> <span id="view-kelamin"></span></p>
            <p><strong>Alamat:</strong> <span id="view-alamat"></span></p>
            <p><strong>Telepon:</strong> <span id="view-telepon"></span></p>
            <p><strong>Role:</strong> <span id="view-role"></span></p>
        </div>
    </div>
</div>

<script>
function openModal(data) {
    document.getElementById('view-username').textContent = data.username;
    document.getElementById('view-email').textContent = data.email;
    document.getElementById('view-fullname').textContent = data.full_name;
    document.getElementById('view-nik').textContent = data.nik;
    document.getElementById('view-tanggal').textContent = data.tanggal_lahir;
    document.getElementById('view-kelamin').textContent = data.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    document.getElementById('view-alamat').textContent = data.alamat;
    document.getElementById('view-telepon').textContent = data.telepon;
    document.getElementById('view-role').textContent = data.role;
    document.getElementById('view-foto').src = 'img/' + data.foto;

    document.getElementById('viewModal').style.display = "block";
}

function closeModal() {
    document.getElementById('viewModal').style.display = "none";
}
</script>
</body>
</html>
