<?php
session_start();
include("connect.php");

// Memeriksa apakah pengguna memiliki akses untuk Pustakawan Jaga I atau IV
if (!isset($_SESSION['ID_ROLE']) || !in_array($_SESSION['ID_ROLE'], [1, 4])) {
    echo "<script>
    alert('Akses tidak diizinkan untuk Anda.');
    window.location.href = 'menu_utama.php';
    </script>";
    exit();
}

// Mengambil jumlah total anggota
$query_total = "SELECT COUNT(*) as total FROM anggota";
$total_anggota = $conn->query($query_total)->fetch_assoc()['total'] ?? 0;

// Mengambil parameter pencarian dan pengurutan
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'ID_ANGGOTA';

// Query untuk mengambil data anggota
$query_anggota = "SELECT a.*, IFNULL(SUM(d.TOTAL_DENDA), 0) AS TUNGGAKAN
FROM anggota a
LEFT JOIN peminjaman p ON a.ID_ANGGOTA = p.ID_ANGGOTA
LEFT JOIN denda d ON p.ID_PEMINJAMAN = d.ID_PEMINJAMAN
WHERE a.NAMA LIKE ?
GROUP BY a.ID_ANGGOTA
ORDER BY $sort";
$stmt = $conn->prepare($query_anggota);
$search_param = '%' . $search . '%';
$stmt->bind_param('s', $search_param);
$stmt->execute();
$result_anggota = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - Sistem Perpustakaan</title>
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
            Data anggota berhasil diperbarui!
        </div>
        <?php endif; ?>

        <!-- Judul -->
        <div class="welcome-container-anggota">
            <h1 class="mb-4">Daftar Anggota</h1>
        </div>

        <div class="container mb-5">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari anggota..."
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
                            <option value="ID_ANGGOTA" <?= $sort === 'ID_ANGGOTA' ? 'selected' : ''; ?>>ID Anggota
                            </option>
                            <option value="NAMA" <?= $sort === 'NAMA' ? 'selected' : ''; ?>>Nama</option>
                            <option value="TANGGAL_JOIN" <?= $sort === 'TANGGAL_JOIN' ? 'selected' : ''; ?>>Tanggal Join
                            </option>
                        </select>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-sort"></i> Urutkan</button>
                    </form>
                </div>
            </div>
            <div class="text-start mb-4" style="margin-bottom: 20px;">
                <h5>Total Anggota: <?= $total_anggota ?></h5>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                        <th>ID Anggota</th>
                        <th>Nama</th>
                        <th>Tanggal Lahir</th>
                        <th>Tanggal Join</th>
                        <th>Alamat</th>
                        <th>No. Telepon</th>
                        <th>Tunggakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_anggota->num_rows > 0): ?>
                    <?php while ($row = $result_anggota->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" name="selected[]" value="<?= $row['ID_ANGGOTA'] ?>"></td>
                        <td><?= htmlspecialchars($row['ID_ANGGOTA'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['NAMA'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['TANGGAL_LAHIR'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['TANGGAL_JOIN'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['ALAMAT'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['KONTAK'], ENT_QUOTES) ?></td>
                        <td>Rp.<?= number_format($row['TUNGGAKAN'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data anggota ditemukan</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="actions">
            <div class="actions d-flex justify-content-between mt-4">
                <a href="hapus_anggota.php" class="btn btn-danger">Hapus</a>
                <a href="tambah_anggota.php" class="btn btn-success">Tambah</a>
                <a href="#" onclick="editSelected()" class="btn btn-warning">Edit</a>
                <a href="convert_anggota_docs.php" class="btn btn-info">Convert to Docs</a>
            </div>
            <div class="footer-anggota">
                &copy; 2024 Sistem Perpustakaan. All rights reserved.
            </div>
        </div>

        <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script>
        function toggleSelectAll(source) {
            document.querySelectorAll('input[name="selected[]"]').forEach(checkbox => checkbox.checked = source
                .checked);
        }

        function editSelected() {
            const selected = document.querySelector('input[name="selected[]"]:checked');
            if (selected) {
                window.location.href = `edit_anggota.php?id_anggota=${selected.value}`;
            } else {
                alert("Pilih anggota yang ingin diedit.");
            }
        }

        // Fungsi untuk membaca parameter dari URL
        function getQueryParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        // Mengecek apakah parameter 'error' ada
        const errorMessage = getQueryParameter('error');
        if (errorMessage) {
            alert(errorMessage); // Menampilkan pesan kesalahan
            // Menghapus parameter 'error' dari URL setelah ditampilkan
            const currentUrl = window.location.href.split('?')[0];
            history.replaceState({}, document.title, currentUrl);
        }
        </script>
</body>

</html>