<?php
session_start();
include("connect.php");

// Memeriksa apakah pengguna memiliki akses yang sesuai
if (!isset($_SESSION['ID_ROLE']) || !in_array($_SESSION['ID_ROLE'], [4])) {
    header("Location: menu_utama.php?error=Akses tidak diizinkan untuk peran Anda.");
    exit();
}

// Mengambil parameter pencarian
$search = $_GET['search'] ?? '';

// Query untuk mengambil data buku
$query_buku = "SELECT * FROM buku WHERE JUDUL LIKE ? ORDER BY ID_BUKU";
$stmt = $conn->prepare($query_buku);
$search_param = '%' . $search . '%';
$stmt->bind_param('s', $search_param);
$stmt->execute();
$result_buku = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
<style>
    h6.mb-0 {
            float: left; 
            margin: 0;
    } 
        
</style>
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
            Data buku berhasil diperbarui!
        </div>
        <?php endif; ?>

        <!-- Judul -->
        <div class="welcome-container-anggota">
            <h6 style class="mb-0">Selamat Datang!</h6>
            <h1 style class="mb-0">Data Buku</h1>
        </div>
            
        <!-- Form Pencarian -->
        <div class="container mb-5">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari buku..."
                                value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i>
                                    Cari</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Buku -->
        <div class="table-container-buku">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                        <th>ID Buku</th>
                        <th>Judul</th>
                        <th>Harga</th>
                        <th>Tipe</th>
                        <th>Genre</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_buku->num_rows > 0): ?>
                    <?php while ($row = $result_buku->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" name="selected[]" value="<?= $row['ID_BUKU'] ?>"></td>
                        <td><?= htmlspecialchars($row['ID_BUKU'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['JUDUL'], ENT_QUOTES) ?></td>
                        <td>Rp.<?= number_format($row['HARGA'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($row['TIPE'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['GENRE'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['STOCK'], ENT_QUOTES) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data buku ditemukan</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tombol Aksi -->
        <div class="actions d-flex justify-content-between mt-4">
            <a href="hapus_buku.php" class="btn btn-danger">Hapus</a>
            <a href="tambah_buku.php" class="btn btn-success">Tambah</a>
            <a href="#" onclick="editSelected()" class="btn btn-warning">Edit</a>
        </div>
    </div>

    <div class="footer-anggota">
        &copy; 2024 Sistem Perpustakaan. All rights reserved.
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleSelectAll(source) {
        document.querySelectorAll('input[name="selected[]"]').forEach(checkbox => checkbox.checked = source.checked);
    }

    function editSelected() {
        const selected = document.querySelector('input[name="selected[]"]:checked');
        if (selected) {
            window.location.href = `edit_buku.php?id_buku=${selected.value}`;
        } else {
            alert("Pilih buku yang ingin diedit.");
        }
    }
    </script>
</body>

</html>