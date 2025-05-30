<?php
include 'koneksi.php';
include 'sidebar.php'; // Assuming sidebar.php contains the admin navigation

session_start();
$admin_id = $_SESSION['id'] ?? null;

// Ensure only logged-in admins can access this page
if (!$admin_id) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE id = $admin_id AND role = 'admin'");
$admin = mysqli_fetch_assoc($query_admin);

if (!$admin) {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini!'); window.location='logout.php';</script>";
    exit;
}

// --- PHP LOGIC FOR CRUD OPERATIONS ---

// Rekrutmen Operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add New Rekrutmen
    if (isset($_POST['add_rekrutmen'])) {
        $posisi = mysqli_real_escape_string($conn, $_POST['posisi']);
        $departemen = mysqli_real_escape_string($conn, $_POST['departemen']);
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        $kualifikasi = mysqli_real_escape_string($conn, $_POST['kualifikasi']);
        $jumlah_kebutuhan = mysqli_real_escape_string($conn, $_POST['jumlah_kebutuhan']);
        $tanggal_buka = mysqli_real_escape_string($conn, $_POST['tanggal_buka']);
        $tanggal_tutup = mysqli_real_escape_string($conn, $_POST['tanggal_tutup']);
        $status = 'dibuka'; // Default status for new recruitment

        $gambar_file = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "img/rekrutmen/"; // Create this folder
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $gambar_file = uniqid() . '_' . basename($_FILES["gambar"]["name"]);
            $target_file = $target_dir . $gambar_file;
            if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                echo "<script>alert('Gagal mengunggah gambar!');</script>";
                $gambar_file = '';
            }
        }

        $stmt = $conn->prepare("INSERT INTO rekrutmen (posisi, departemen, deskripsi, kualifikasi, gambar, jumlah_kebutuhan, tanggal_buka, tanggal_tutup, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("sssssisss", $posisi, $departemen, $deskripsi, $kualifikasi, $gambar_file, $jumlah_kebutuhan, $tanggal_buka, $tanggal_tutup, $status);

        if ($stmt->execute()) {
            echo "<script>alert('Rekrutmen baru berhasil ditambahkan!'); window.location='admin-rekrutmen.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }

    // Edit Rekrutmen
    if (isset($_POST['edit_rekrutmen'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $posisi = mysqli_real_escape_string($conn, $_POST['posisi']);
        $departemen = mysqli_real_escape_string($conn, $_POST['departemen']);
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        $kualifikasi = mysqli_real_escape_string($conn, $_POST['kualifikasi']);
        $jumlah_kebutuhan = mysqli_real_escape_string($conn, $_POST['jumlah_kebutuhan']);
        $tanggal_buka = mysqli_real_escape_string($conn, $_POST['tanggal_buka']);
        $tanggal_tutup = mysqli_real_escape_string($conn, $_POST['tanggal_tutup']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $old_gambar = mysqli_real_escape_string($conn, $_POST['old_gambar']);

        $gambar_file = $old_gambar; // Keep old image by default
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "img/rekrutmen/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $gambar_file = uniqid() . '_' . basename($_FILES["gambar"]["name"]);
            $target_file = $target_dir . $gambar_file;

            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                // Delete old image if new one is uploaded
                if ($old_gambar && file_exists($target_dir . $old_gambar)) {
                    unlink($target_dir . $old_gambar);
                }
            } else {
                echo "<script>alert('Gagal mengunggah gambar baru, menggunakan gambar lama.');</script>";
                $gambar_file = $old_gambar;
            }
        }

        $stmt = $conn->prepare("UPDATE rekrutmen SET posisi=?, departemen=?, deskripsi=?, kualifikasi=?, gambar=?, jumlah_kebutuhan=?, tanggal_buka=?, tanggal_tutup=?, status=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssssisssi", $posisi, $departemen, $deskripsi, $kualifikasi, $gambar_file, $jumlah_kebutuhan, $tanggal_buka, $tanggal_tutup, $status, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Rekrutmen berhasil diperbarui!'); window.location='admin-rekrutmen.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }

    // Update Pelamar Status
    if (isset($_POST['update_pelamar_status'])) {
        $pelamar_id = mysqli_real_escape_string($conn, $_POST['pelamar_id']);
        $status_baru = mysqli_real_escape_string($conn, $_POST['status_baru']);

        $stmt = $conn->prepare("UPDATE pelamar SET status_lamaran=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("si", $status_baru, $pelamar_id);

        if ($stmt->execute()) {
            echo "<script>alert('Status pelamar berhasil diperbarui!'); window.location='admin-rekrutmen.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}

// Handle GET requests for deletion
if (isset($_GET['delete_rekrutmen_id']) && is_numeric($_GET['delete_rekrutmen_id'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['delete_rekrutmen_id']);

    // Get image path before deleting record
    $query_gambar = mysqli_query($conn, "SELECT gambar FROM rekrutmen WHERE id = $id_hapus");
    $row_gambar = mysqli_fetch_assoc($query_gambar);
    $gambar_to_delete = $row_gambar['gambar'] ?? '';

    // Delete associated pelamar records first (if no cascade delete is set in DB)
    mysqli_query($conn, "DELETE FROM pelamar WHERE rekrutmen_id = $id_hapus");

    // Delete rekrutmen record
    $query_hapus_rekrutmen = mysqli_query($conn, "DELETE FROM rekrutmen WHERE id = $id_hapus");

    if ($query_hapus_rekrutmen) {
        // Delete image file from server
        if ($gambar_to_delete && file_exists("img/rekrutmen/" . $gambar_to_delete)) {
            unlink("img/rekrutmen/" . $gambar_to_delete);
        }
        echo "<script>alert('Rekrutmen dan pelamar terkait berhasil dihapus!'); window.location='admin-rekrutmen.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus rekrutmen!');</script>";
    }
}

if (isset($_GET['delete_pelamar_id']) && is_numeric($_GET['delete_pelamar_id'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['delete_pelamar_id']);

    // Get CV file path before deleting record
    $query_cv = mysqli_query($conn, "SELECT cv_file FROM pelamar WHERE id = $id_hapus");
    $row_cv = mysqli_fetch_assoc($query_cv);
    $cv_to_delete = $row_cv['cv_file'] ?? '';

    $query_hapus_pelamar = mysqli_query($conn, "DELETE FROM pelamar WHERE id = $id_hapus");

    if ($query_hapus_pelamar) {
        // Delete CV file from server
        if ($cv_to_delete && file_exists("cv/" . $cv_to_delete)) {
            unlink("cv/" . $cv_to_delete);
        }
        echo "<script>alert('Pelamar berhasil dihapus!'); window.location='admin-rekrutmen.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus pelamar!');</script>";
    }
}

// Fetch data for display
$rekrutmen_data = mysqli_query($conn, "SELECT * FROM rekrutmen ORDER BY tanggal_buka DESC");
$pelamar_data = mysqli_query($conn, "SELECT p.*, r.posisi FROM pelamar p JOIN rekrutmen r ON p.rekrutmen_id = r.id ORDER BY p.created_at DESC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Rekrutmen & Pelamar - Dharma Hotel Admin</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Custom CSS based on your admin-reservasi.php style */
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
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-hapus { background-color: #dc3545; color: white; }
        .btn-hapus:hover { background-color: #c82333; }
        .btn-edit { background-color: #007bff; color: white; }
        .btn-edit:hover { background-color: #0056b3; }
        .btn-success-custom { background-color: #28a745; color: white; }
        .btn-success-custom:hover { background-color: #218838; }
        .btn-info-custom { background-color: #17a2b8; color: white; }
        .btn-info-custom:hover { background-color: #138496; }
        .btn-warning-custom { background-color: #ffc107; color: #212529; }
        .btn-warning-custom:hover { background-color: #e0a800; }
        .btn-secondary-custom { background-color: #6c757d; color: white; }
        .btn-secondary-custom:hover { background-color: #5a6268; }


        /* Tab styles */
        .nav-tabs .nav-link {
            color: #495057;
            background-color: #e9ecef;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1050; /* Sit on top of other content, below Bootstrap's default modal z-index if using it */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
        }
        .modal-dialog {
            margin: 1.75rem auto;
            max-width: 700px; /* Adjust max-width as needed */
            position: relative;
            pointer-events: none;
        }
        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: .3rem;
            outline: 0;
        }
        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 1rem 1rem;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: .3rem;
            border-top-right-radius: .3rem;
        }
        .modal-header .close {
            padding: 1rem 1rem;
            margin: -1rem -1rem -1rem auto;
        }
        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
        }
        .modal-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            padding: .75rem;
            border-top: 1px solid #dee2e6;
            border-bottom-right-radius: .3rem;
            border-bottom-left-radius: .3rem;
        }
        .close-button {
            float: right;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            text-decoration: none;
        }
        .close-button:hover {
            color: #000;
            text-decoration: none;
            opacity: .75;
        }
        .form-group label {
            margin-bottom: .5rem;
            font-weight: 600;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            display: block;
            width: 100%;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .form-group textarea {
            min-height: 80px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="table-container">
            <h2>Manajemen Rekrutmen & Pelamar</h2>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="rekrutmen" role="tabpanel" aria-labelledby="rekrutmen-tab">
                    <h3 class="mt-3">Daftar Rekrutmen</h3>
                    <button type="button" class="btn btn-success-custom mb-3" data-bs-toggle="modal" data-bs-target="#rekrutmenModal" onclick="resetRekrutmenForm()">Tambah Rekrutmen Baru</button>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Posisi</th>
                                <th>Departemen</th>
                                <th>Gambar</th>
                                <th>Jumlah Kebutuhan</th>
                                <th>Tgl Buka</th>
                                <th>Tgl Tutup</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($rekrutmen_data) == 0): ?>
                                <tr><td colspan="9" class="text-center">Tidak ada data rekrutmen.</td></tr>
                            <?php else: ?>
                                <?php while ($rekrutmen = mysqli_fetch_assoc($rekrutmen_data)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($rekrutmen['id']) ?></td>
                                        <td><?= htmlspecialchars($rekrutmen['posisi']) ?></td>
                                        <td><?= htmlspecialchars($rekrutmen['departemen']) ?></td>
                                        <td>
                                            <?php if ($rekrutmen['gambar']): ?>
                                                <img src="img/rekrutmen/<?= htmlspecialchars($rekrutmen['gambar']) ?>" alt="Gambar" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                Tidak Ada
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($rekrutmen['jumlah_kebutuhan']) ?></td>
                                        <td><?= htmlspecialchars($rekrutmen['tanggal_buka']) ?></td>
                                        <td><?= htmlspecialchars($rekrutmen['tanggal_tutup']) ?></td>
                                        <td><?= htmlspecialchars($rekrutmen['status']) ?></td>
                                        <td>
                                            <button type="button" class="aksi-btn btn-edit" onclick="editRekrutmen(<?= htmlspecialchars(json_encode($rekrutmen)) ?>)">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <a href="?delete_rekrutmen_id=<?= $rekrutmen['id'] ?>" class="aksi-btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus rekrutmen ini? Ini akan menghapus semua pelamar yang terkait.')">
                                                <i class="fa fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="pelamar" role="tabpanel" aria-labelledby="pelamar-tab">
                    <h3 class="mt-3">Daftar Pelamar</h3>

                    <div class="mb-3">
                        <label for="filter_rekrutmen_pelamar" class="form-label">Filter berdasarkan Lowongan:</label>
                        <select id="filter_rekrutmen_pelamar" class="form-select" onchange="filterPelamar()">
                            <option value="">Semua Lowongan</option>
                            <?php
                            // Reset pointer for rekrutmen_data if already fetched
                            if (mysqli_num_rows($rekrutmen_data) > 0) {
                                mysqli_data_seek($rekrutmen_data, 0);
                                while ($rec = mysqli_fetch_assoc($rekrutmen_data)): ?>
                                    <option value="<?= htmlspecialchars($rec['id']) ?>"><?= htmlspecialchars($rec['posisi']) ?> (<?= htmlspecialchars($rec['departemen']) ?>)</option>
                                <?php endwhile;
                            }
                            ?>
                        </select>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Lowongan</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>CV</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pelamar_table_body">
                            <?php if (mysqli_num_rows($pelamar_data) == 0): ?>
                                <tr><td colspan="8" class="text-center">Tidak ada data pelamar.</td></tr>
                            <?php else: ?>
                                <?php while ($pelamar = mysqli_fetch_assoc($pelamar_data)): ?>
                                    <tr data-rekrutmen-id="<?= htmlspecialchars($pelamar['rekrutmen_id']) ?>">
                                        <td><?= htmlspecialchars($pelamar['id']) ?></td>
                                        <td><?= htmlspecialchars($pelamar['posisi']) ?></td>
                                        <td><?= htmlspecialchars($pelamar['nama_lengkap']) ?></td>
                                        <td><?= htmlspecialchars($pelamar['email']) ?></td>
                                        <td><?= htmlspecialchars($pelamar['no_hp']) ?></td>
                                        <td>
                                            <?php if ($pelamar['cv_file']): ?>
                                                <a href="cv/<?= htmlspecialchars($pelamar['cv_file']) ?>" target="_blank" class="btn btn-info-custom aksi-btn">
                                                    <i class="fa fa-file-pdf"></i> Lihat CV
                                                </a>
                                            <?php else: ?>
                                                Tidak Ada
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($pelamar['status_lamaran']) ?></td>
                                        <td>
                                            <button type="button" class="aksi-btn btn-warning-custom" onclick="openUpdateStatusModal(<?= $pelamar['id'] ?>, '<?= htmlspecialchars($pelamar['status_lamaran']) ?>')">
                                                <i class="fa fa-sync-alt"></i> Ubah Status
                                            </button>
                                            <a href="?delete_pelamar_id=<?= $pelamar['id'] ?>" class="aksi-btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus pelamar ini? File CV juga akan dihapus.')">
                                                <i class="fa fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rekrutmenModal" tabindex="-1" aria-labelledby="rekrutmenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rekrutmenForm" action="admin-rekrutmen.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rekrutmenModalLabel">Tambah Rekrutmen Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="rekrutmen_id">
                        <input type="hidden" name="old_gambar" id="old_gambar">

                        <div class="mb-3">
                            <label for="posisi" class="form-label">Posisi:</label>
                            <input type="text" class="form-control" id="posisi" name="posisi" required>
                        </div>
                        <div class="mb-3">
                            <label for="departemen" class="form-label">Departemen:</label>
                            <input type="text" class="form-control" id="departemen" name="departemen" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi:</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="kualifikasi" class="form-label">Kualifikasi:</label>
                            <textarea class="form-control" id="kualifikasi" name="kualifikasi" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar (Opsional):</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="img/rekrutmen/">
                            <small class="form-text text-muted" id="current_gambar_text">Gambar saat ini: -</small>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah_kebutuhan" class="form-label">Jumlah Kebutuhan:</label>
                            <input type="number" class="form-control" id="jumlah_kebutuhan" name="jumlah_kebutuhan" required min="1">
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_buka" class="form-label">Tanggal Buka:</label>
                            <input type="date" class="form-control" id="tanggal_buka" name="tanggal_buka" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_tutup" class="form-label">Tanggal Tutup:</label>
                            <input type="date" class="form-control" id="tanggal_tutup" name="tanggal_tutup" required>
                        </div>
                        <div class="mb-3" id="statusRekrutmenGroup" style="display:none;">
                            <label for="status_rekrutmen" class="form-label">Status:</label>
                            <select class="form-select" id="status_rekrutmen" name="status">
                                <option value="dibuka">Dibuka</option>
                                <option value="ditutup">Ditutup</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success-custom" name="add_rekrutmen" id="submitRekrutmenBtn">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="admin-rekrutmen.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateStatusModalLabel">Ubah Status Pelamar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="pelamar_id" id="pelamar_id_status">
                        <div class="mb-3">
                            <label for="status_lamaran_select" class="form-label">Status Lamaran:</label>
                            <select class="form-select" id="status_lamaran_select" name="status_baru">
                                <option value="baru">Baru</option>
                                <option value="diproses">Diproses</option>
                                <option value="diterima">Diterima</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success-custom" name="update_pelamar_status">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk mereset form rekrutmen saat menambah baru
        function resetRekrutmenForm() {
            $('#rekrutmenModalLabel').text('Tambah Rekrutmen Baru');
            $('#rekrutmenForm').trigger('reset');
            $('#rekrutmen_id').val('');
            $('#old_gambar').val('');
            $('#current_gambar_text').text('Gambar saat ini: -');
            $('#submitRekrutmenBtn').attr('name', 'add_rekrutmen').text('Tambah');
            $('#statusRekrutmenGroup').hide(); // Sembunyikan status saat menambah baru
        }

        // Fungsi untuk mengisi form edit rekrutmen
        function editRekrutmen(rekrutmen) {
            $('#rekrutmenModalLabel').text('Edit Rekrutmen');
            $('#submitRekrutmenBtn').attr('name', 'edit_rekrutmen').text('Simpan Perubahan');
            $('#statusRekrutmenGroup').show(); // Tampilkan status saat mengedit

            $('#rekrutmen_id').val(rekrutmen.id);
            $('#posisi').val(rekrutmen.posisi);
            $('#departemen').val(rekrutmen.departemen);
            $('#deskripsi').val(rekrutmen.deskripsi);
            $('#kualifikasi').val(rekrutmen.kualifikasi);
            $('#old_gambar').val(rekrutmen.gambar);
            if (rekrutmen.gambar) {
                $('#current_gambar_text').html('Gambar saat ini: <a href="img/rekrutmen/' + rekrutmen.gambar + '" target="_blank">' + rekrutmen.gambar + '</a>');
            } else {
                $('#current_gambar_text').text('Gambar saat ini: Tidak ada');
            }
            $('#jumlah_kebutuhan').val(rekrutmen.jumlah_kebutuhan);
            $('#tanggal_buka').val(rekrutmen.tanggal_buka);
            $('#tanggal_tutup').val(rekrutmen.tanggal_tutup);
            $('#status_rekrutmen').val(rekrutmen.status);

            var rekrutmenModal = new bootstrap.Modal(document.getElementById('rekrutmenModal'));
            rekrutmenModal.show();
        }

        // Fungsi untuk membuka modal ubah status pelamar
        function openUpdateStatusModal(pelamarId, currentStatus) {
            $('#pelamar_id_status').val(pelamarId);
            $('#status_lamaran_select').val(currentStatus);
            var updateStatusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
            updateStatusModal.show();
        }

        // Fungsi filter pelamar berdasarkan rekrutmen_id
        function filterPelamar() {
            var selectedRekrutmenId = $('#filter_rekrutmen_pelamar').val();
            var tableRows = $('#pelamar_table_body tr');

            tableRows.each(function() {
                var rowRekrutmenId = $(this).data('rekrutmen-id');
                if (selectedRekrutmenId === "" || rowRekrutmenId == selectedRekrutmenId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Inisialisasi tab Bootstrap
        document.addEventListener('DOMContentLoaded', function () {
            var triggerTabList = [].slice.call(document.querySelectorAll('#myTab button'))
            triggerTabList.forEach(function (triggerTab) {
                var tabTrigger = new bootstrap.Tab(triggerTab)

                triggerTab.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>