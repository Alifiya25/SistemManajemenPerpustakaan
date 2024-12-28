<?php
session_start();
include("connect.php");

// Memeriksa apakah pengguna memiliki akses untuk Pustakawan Jaga IV
if (!isset($_SESSION['ID_ROLE']) || $_SESSION['ID_ROLE'] != 4) {
    header("Location: menu_utama.php?error=Akses tidak diizinkan untuk peran Anda.");
    exit();
}

// Mengambil jumlah total pustakawan
$query_total = "SELECT COUNT(*) as total FROM pustakawan";
$total_pustakawan = $conn->query($query_total)->fetch_assoc()['total'] ?? 0;

// Mengambil parameter pencarian dan pengurutan
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'ID_PUSTAKAWAN';

// Query untuk mengambil data pustakawan
$query_pustakawan = "SELECT * FROM pustakawan WHERE NAMA LIKE ? ORDER BY $sort";
$stmt = $conn->prepare($query_pustakawan);
$search_param = '%' . $search . '%';
$stmt->bind_param('s', $search_param);
$stmt->execute();
$result_pustakawan = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pustakawan - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="title">SMARTLIB</div>
        <div class="nav-buttons">
            <a href="menu_utama.php"><i class="fas fa-home"></i> Menu Utama</a>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Pesan sukses setelah update -->
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success" role="alert">
            Data pustakawan berhasil diperbarui!
        </div>
        <?php endif; ?>

        <!-- Judul -->
        <div class="welcome-container-anggota">
            <h1 class="mb-4">Daftar Pustakawan</h1>
        </div>

        <div class="container mb-5">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari pustakawan..."
                                value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i>
                                    Cari</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    <form method="GET" action="" class="d-flex align-items-center justify-content-end">
                        <label for="sort" class="mr-2">Urutkan:</label>
                        <select name="sort" id="sort" class="form-control mr-2 w-auto">
                            <option value="ID_PUSTAKAWAN" <?= $sort === 'ID_PUSTAKAWAN' ? 'selected' : ''; ?>>ID
                                Pustakawan
                            </option>
                            <option value="NAMA" <?= $sort === 'NAMA' ? 'selected' : ''; ?>>Nama</option>
                            <option value="JABATAN" <?= $sort === 'JABATAN' ? 'selected' : ''; ?>>Jabatan</option>
                        </select>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-sort"></i> Urutkan</button>
                    </form>
                </div>
            </div>
            <div class="text-start mb-4" style="margin-bottom: 20px;">
                <h5>Total Pustakawan: <?= $total_pustakawan ?></h5>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                        <th>ID Pustakawan</th>
                        <th>Nama</th>
                        <th>Tanggal Lahir</th>
                        <th>Tanggal Pustakawan</th>
                        <th>Alamat</th>
                        <th>No. Telepon</th>
                        <th>Jabatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_pustakawan->num_rows > 0): ?>
                    <?php while ($row = $result_pustakawan->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" name="selected[]" value="<?= $row['ID_PUSTAKAWAN'] ?>"></td>
                        <td><?= htmlspecialchars($row['ID_PUSTAKAWAN'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['NAMA'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['TANGGAL_LAHIR'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['TANGGAL_PUSTAKAWAN'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['ALAMAT'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['KONTAK'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['JABATAN'], ENT_QUOTES) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data pustakawan ditemukan</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="actions d-flex justify-content-between mt-4">
            <a href="hapus_pustakawan.php" class="btn btn-danger">Kosongkan</a>
            <a href="#" onclick="editSelected()" class="btn btn-warning">Edit</a>
            <a href="tambah_pustakawan.php" class="btn btn-success">Tambah</a>
        </div>

        <div class="footer-anggota">
            &copy; 2024 Sistem Perpustakaan. All rights reserved.
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleSelectAll(source) {
        document.querySelectorAll('input[name="selected[]"]').forEach(checkbox => checkbox.checked = source.checked);
    }

    function editSelected() {
        const selected = document.querySelector('input[name="selected[]"]:checked');
        if (selected) {
            window.location.href = `edit_pustakawan.php?id_pustakawan=${selected.value}`;
        } else {
            alert("Pilih pustakawan yang ingin diedit.");
        }
    }
    </script>
</body>

</html>