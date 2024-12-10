<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['NAMA'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Utama - Sistem Perpustakaan</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- Include FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="title">SMARTLIB</div>
        <div class="nav-buttons">
            <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Selamat Datang -->
    <div class="welcome-container">
        <div class="welcome-message">
            Selamat Datang di Sistem Perpustakaan, <br>
            <span><?php echo htmlspecialchars($_SESSION['NAMA'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
    </div>

    <!-- Kontainer Pesan Kesalahan -->
    <div id="error-container" class="error-container">
        <p id="error-message">Anda tidak memiliki akses ke halaman Anggota.</p>
    </div>

    <!-- Menu Utama -->
    <div class="menu-container">
        <div class="menu-grid">
            <a href="anggota.php" class="menu-card">
                <i class="fas fa-users"></i>
                <h4>Anggota</h4>
            </a>
            <a href="pustakawan.php" class="menu-card">
                <i class="fas fa-user-tie"></i>
                <h4>Pustakawan</h4>
            </a>
            <a href="buku.php" class="menu-card">
                <i class="fas fa-book"></i>
                <h4>Buku</h4>
            </a>
            <a href="denda.php" class="menu-card">
                <i class="fas fa-file-invoice-dollar"></i>
                <h4>Denda</h4>
            </a>
            <a href="peminjaman.php" class="menu-card">
                <i class="fas fa-book-open"></i>
                <h4>Peminjaman</h4>
            </a>
            <a href="pengembalian.php" class="menu-card">
                <i class="fas fa-undo-alt"></i>
                <h4>Pengembalian</h4>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-menu">
        &copy; 2024 Sistem Perpustakaan. All rights reserved.
    </div>

    <!-- Include Bootstrap JS -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    // Cek apakah URL mengandung parameter "error" dengan nilai "akses"
    <?php if (isset($_GET['error']) && $_GET['error'] == 'akses') : ?>
    // Jika ya, tampilkan kontainer pesan kesalahan
    document.getElementById('error-container').style.display = 'block';
    <?php endif; ?>
    </script>
</body>

</html>