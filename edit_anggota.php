<?php
include('connect.php');

// Cek apakah ID anggota ada di URL
if (isset($_GET['id_anggota'])) {
    $id_anggota = mysqli_real_escape_string($conn, $_GET['id_anggota']);

    // Query untuk mengambil data anggota berdasarkan ID
    $query = "SELECT * FROM anggota WHERE ID_ANGGOTA = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $id_anggota);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Cek apakah data ditemukan
    if ($result && mysqli_num_rows($result) > 0) {
        $anggota = mysqli_fetch_assoc($result);
    } else {
        echo "<div class='alert alert-danger'>Anggota tidak ditemukan.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger'>ID Anggota tidak ditemukan di URL.</div>";
    exit;
}

// Handle pengiriman data melalui metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan validasi input
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $tanggal_lahir = mysqli_real_escape_string($conn, trim($_POST['tanggal_lahir']));
    $tanggal_join = mysqli_real_escape_string($conn, trim($_POST['tanggal_join']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $kontak = mysqli_real_escape_string($conn, trim($_POST['kontak']));

    // Validasi input tidak boleh kosong
    if (empty($nama_lengkap) || empty($tanggal_lahir) || empty($tanggal_join) || empty($alamat) || empty($kontak)) {
        echo "<div class='alert alert-danger'>Semua field harus diisi.</div>";
    } else {
        // Query untuk mengupdate data anggota
        $query = "UPDATE anggota SET NAMA = ?, TANGGAL_LAHIR = ?, TANGGAL_JOIN = ?, ALAMAT = ?, KONTAK = ? WHERE ID_ANGGOTA = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $nama_lengkap, $tanggal_lahir, $tanggal_join, $alamat, $kontak, $id_anggota);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: anggota.php?status=success");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error saat memperbarui data: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="title">SMARTLIB</div>
        <div class="nav-buttons">
            <a href="anggota.php"><i class="fas fa-sign-out-alt"></i> Back</a>
        </div>
    </div>

    <div class="welcome-container-anggota">
        <h2 class="mb-4">Edit Anggota</h2>

        <form method="POST" action="">
            <input type="hidden" name="id_anggota"
                value="<?php echo htmlspecialchars($anggota['ID_ANGGOTA'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="mb-3 row">
                <label for="nama_lengkap" class="col-sm-2 col-form-label">Nama Lengkap</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required
                        value="<?php echo htmlspecialchars($anggota['NAMA'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="mb-3 row">
                <label for="tanggal_lahir" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required
                        value="<?php echo htmlspecialchars($anggota['TANGGAL_LAHIR'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="mb-3 row">
                <label for="tanggal_join" class="col-sm-2 col-form-label">Tanggal Join</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="tanggal_join" name="tanggal_join" required
                        value="<?php echo htmlspecialchars($anggota['TANGGAL_JOIN'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="mb-3 row">
                <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="alamat" name="alamat" rows="3"
                        required><?php echo htmlspecialchars($anggota['ALAMAT'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="kontak" class="col-sm-2 col-form-label">Kontak</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="kontak" name="kontak" required
                        value="<?php echo htmlspecialchars($anggota['KONTAK'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-sm-10 offset-sm-2">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="anggota.php" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>

        <footer class="footer-anggota mt-4">
            &copy; 2024 Sistem Perpustakaan. All rights reserved.
        </footer>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>