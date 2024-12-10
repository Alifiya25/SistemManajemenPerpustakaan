<?php
session_start();
include 'connect.php';

// Periksa apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan validasi input dari form
    $id_pustakawan = $_POST['ID_PUSTAKAWAN'] ?? '';
    $nama = $_POST['NAMA'] ?? '';

    if (!$id_pustakawan || !$nama) {
        $_SESSION['error_message'] = "Harap isi semua field!";
    } else {
        // Query dan eksekusi untuk memeriksa ID_PUSTAKAWAN dan NAMA
        $stmt = $conn->prepare("SELECT ID_ROLE FROM pustakawan WHERE ID_PUSTAKAWAN = ? AND NAMA = ?");
        $stmt->bind_param("ss", $id_pustakawan, $nama);
        $stmt->execute();
        $result = $stmt->get_result();

        // Jika data ditemukan, set session dan redirect
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['ID_PUSTAKAWAN'] = $id_pustakawan;
            $_SESSION['NAMA'] = $nama;
            $_SESSION['ID_ROLE'] = $user['ID_ROLE'];
            header("Location: menu_utama.php");
            exit();
        } else {
            $_SESSION['error_message'] = "ID Pustakawan atau Nama salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartLib - Login</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>SMARTLIB</h1>
            <p>Login untuk mengakses sistem perpustakaan</p>
        </div>

        <!-- Menampilkan pesan kesalahan jika ada -->
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message"><?= $_SESSION['error_message'] ?></div>
        <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form action="login.php" method="POST" id="login-form">
            <div class="form-group mb-3">
                <label for="ID_PUSTAKAWAN">ID Pustakawan:</label>
                <input type="text" class="form-control" id="ID_PUSTAKAWAN" name="ID_PUSTAKAWAN"
                    placeholder="Masukkan ID Pustakawan" required>
            </div>
            <div class="form-group mb-3">
                <label for="NAMA">Nama:</label>
                <input type="text" class="form-control" id="NAMA" name="NAMA" placeholder="Masukkan Nama Anda" required>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>

        <div class="footer-text">
            &copy; 2024 SmartLib. Semua hak cipta dilindungi.
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>