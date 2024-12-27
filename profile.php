<?php
session_start();
include 'connect.php';

// Periksa apakah pengguna telah login
if (!isset($_SESSION['ID_PUSTAKAWAN']) || !isset($_SESSION['NAMA'])) {
    header("Location: login.php");
    exit();
}

$id_pustakawan = $_SESSION['ID_PUSTAKAWAN'];

// Ambil data pustakawan dari database
$sql = "SELECT p.NAMA, p.KONTAK, p.ALAMAT, p.TANGGAL_LAHIR, p.JABATAN, r.NAMA_ROLE 
        FROM pustakawan p INNER JOIN roles r ON p.ID_ROLE = r.ID_ROLE WHERE p.ID_PUSTAKAWAN = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pustakawan);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data pustakawan tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
    body {
        background-color: #d1ecd1;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .container {
        margin-top: 50px;
        max-width: 600px;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 8px #4caf50;
        text-align: center;
    }

    .profile-header img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid #4caf50;
        margin-bottom: 15px;
    }

    .profile-header h1 {
        font-size: 24px;
        margin: 0;
        color: #4caf50;
    }

    .profile-header p {
        color: #666;
        margin-bottom: 20px;
    }

    .profile-info {
        text-align: left;
        margin-top: 20px;
    }

    .info-item {
        margin-bottom: 10px;
    }

    .info-label {
        font-weight: bold;
        color: #555;
    }

    .btn {
        background-color: #4caf50;
        color: white;
        border: none;
        padding: 10px 20px;
        margin-top: 20px;
        border-radius: 8px;
    }

    .btn:hover {
        background-color: #388e3c;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="profile-header">
            <h1><?php echo htmlspecialchars($data['NAMA'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <p><?php echo htmlspecialchars($data['JABATAN'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="profile-info">
            <div class="info-item">
                <span class="info-label">ID Pustakawan:</span>
                <?php echo htmlspecialchars($id_pustakawan, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="info-item">
                <span class="info-label">Nama:</span>
                <?php echo htmlspecialchars($data['NAMA'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="info-item">
                <span class="info-label">Role:</span>
                <?php echo htmlspecialchars($data['NAMA_ROLE'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="info-item">
                <span class="info-label">Kontak:</span>
                <?php echo htmlspecialchars($data['KONTAK'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="info-item">
                <span class="info-label">Alamat:</span>
                <?php echo htmlspecialchars($data['ALAMAT'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Lahir:</span>
                <?php echo htmlspecialchars($data['TANGGAL_LAHIR'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
        <a href="menu_utama.php" class="btn">Kembali ke Menu Utama</a>
    </div>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>