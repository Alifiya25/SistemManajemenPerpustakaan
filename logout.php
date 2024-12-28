<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
// Jika Pengguna mengonfirmasi logout
session_unset();
session_destroy();

// Redirect ke halaman index.html
header("Location: login.php");
exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
    body {
        background-color: #d1ecd1;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
    }

    .logout-container {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px #4caf50;
        text-align: center;
        animation: fadeln 0.8s ease-in-out;
        max-width: 300px;
        width: 100%;
    }

    .logout-container h1 {
        font-size: 22px;
        color: #00695c;
        margin-bottom: 15px;
        animation: pulse 1.5s infinite;
    }

    .logout-container p {
        font-size: 14px;
        color: #555;
        margin-bottom: 20px;
    }

    .btn {
        margin: 5px;
        padding: 10px 20px;
        font-size: 14px;
        border: none;
        border-radius: 6px;
        transition: transform 0.3s ease, background-color 0.3s ease;
        cursor: pointer;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-logout {
        background-color: #4caf50;
        color: #ffffff;
    }

    .btn-logout:hover {
        background-color: #388e3c;
    }

    .btn-cancel {
        background-color: #ff0000;
        color: #ffffff;
    }

    .btn-cancel:hover {
        background-color: #d32f2f;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.05);
            opacity: 0.8;
        }
    }
    </style>
</head>

<body>
    <div class="logout-container">
        <h1>Konfirmasi Logout</h1>
        <p>Anda akan keluar dari sesi ini, Apakah Anda yakin ingin melanjutkan ?</p>
        <form method="POST" action="">
            <button type="submit" class="btn btn-logout">Ya</button>
            <a href="menu_utama.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>
    <!-- Include Bootstrap JS -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>