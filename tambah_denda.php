<?php
// Mulai sesi jika belum dimulai
session_start();

// Menghubungkan ke database
include 'connect.php';

// Mengambil ID_Denda terakhir untuk menghitung ID berikutnya
$query = "SELECT ID_DENDA FROM denda ORDER BY ID_DENDA DESC LIMIT 1";
$result = $conn->query($query);
$next_id = '101'; // Default jika tidak ada data
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Menghitung ID_Denda berikutnya dengan menambah 1 pada ID terakhir
    $last_id = $row['ID_DENDA'];
    $next_id = str_pad($last_id + 1, 6, '0', STR_PAD_LEFT);
}

// Mengecek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mendapatkan data dari form
    $id_peminjaman = $_POST['id_peminjaman'];
    $id_pustakawan = $_POST['id_pustakawan'];
    $id_buku = $_POST['id_buku'];
    $total_denda = $_POST['total_denda'];
    $jumlah_buku = $_POST['jumlah_buku'];
    $tanggal_denda = $_POST['tanggal_denda'];

    // Query untuk memasukkan data denda
    $sql = "INSERT INTO denda (ID_DENDA, ID_PEMINJAMAN, ID_PUSTAKAWAN, ID_BUKU, TOTAL_DENDA, JUMLAH_BUKU, TANGGAL_DENDA)
            VALUES ('$next_id', '$id_peminjaman', '$id_pustakawan', '$id_buku', '$total_denda', '$jumlah_buku', '$tanggal_denda')";

    // Eksekusi query
    if ($conn->query($sql) === TRUE) {
        // Menyimpan status ke session
        $_SESSION['status'] = 'success';
        header("Location: denda.php"); // Redirect setelah insert berhasil
        exit(); // Pastikan script berhenti
    } else {
        // Menyimpan error ke session
        $_SESSION['status'] = 'error';
        header("Location: tambah_denda.php"); // Redirect kembali ke halaman
        exit(); // Pastikan script berhenti
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tambah Denda</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', Arial, sans-serif;
        background-color: #d1ecd1;
        color: #333;
        height: 100vh;
        overflow: hidden;
    }

    .header {
        background: #4caf50;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .header .title {
        font-size: 22px;
        font-weight: bold;
    }

    .header .nav-buttons a {
        color: white;
        font-size: 16px;
        font-weight: bold;
        margin-left: 10px;
        text-decoration: none;
        padding: 8px 16px;
        border: 2px solid white;
        border-radius: 30px;
        transition: all 0.3s ease-in-out;
    }

    .header .nav-buttons a:hover {
        background-color: white;
        color: #4caf50;
    }

    .welcome-container {
        padding: 15px 10px;
        text-align: center;
        font-size: 28px;
        font-weight: bold;
        color: #4caf50;
    }

    /* Styling untuk container */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        min-height: 100px;
        /* Tinggi minimal */
        max-height: 60vh;
        /* Tinggi maksimal 80% viewport */
        overflow-y: auto;
        /* Scroll jika konten berlebih */
        box-sizing: border-box;
    }

    /* Styling label dan input agar bersebelahan */
    .form-group {
        display: grid;
        grid-template-columns: 150px auto;
        /* Kolom kiri untuk label, kanan untuk input */
        gap: 10px;
        /* Jarak antar elemen */
        align-items: center;
        margin-bottom: 15px;
    }

    .form-label {
        font-weight: bold;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    /* Button Styling */
    .btn-success {
        padding: 8px 20px;
        font-size: 16px;
        border-radius: 5px;
    }

    /* Hover effect */
    .btn-success:hover {
        background-color: #28a745;
        color: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .footer {
        background: #4caf50;
        color: white;
        text-align: center;
        padding: 10px 0;
        bottom: 0;
        position: fixed;
        left: 0;
        width: 100%;
        z-index: 100;
        /* Pastikan footer berada di atas konten lain */
    }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="title">SMARTLIB</div>
        <div class="nav-buttons">
            <a href="denda.php"><i class="fas fa-home"></i> Back</a>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="welcome-container">
        Form Tambah Denda
    </div>

    <div class="container mt-4">
        <form action="" method="POST">
            <div class="form-group">
                <label for="id_denda" class="form-label">ID Denda</label>
                <input type="text" id="id_denda" name="id_denda" class="form-control" value="<?php echo $next_id; ?>"
                    readonly>
            </div>
            <div class="form-group">
                <label for="id_peminjaman" class="form-label">ID Peminjaman</label>
                <input type="text" id="id_peminjaman" name="id_peminjaman" class="form-control"
                    placeholder="Masukkan ID Peminjaman" required>
            </div>
            <div class="form-group">
                <label for="id_pustakawan" class="form-label">ID Pustakawan</label>
                <input type="text" id="id_pustakawan" name="id_pustakawan" class="form-control"
                    placeholder="Masukkan ID Pustakawan" required>
            </div>
            <div class="form-group">
                <label for="id_buku" class="form-label">ID Buku</label>
                <input type="text" id="id_buku" name="id_buku" class="form-control" placeholder="Masukkan ID Buku"
                    required>
            </div>
            <div class="form-group">
                <label for="total_denda" class="form-label">Total Denda</label>
                <input type="number" id="total_denda" name="total_denda" class="form-control"
                    placeholder="Masukkan Total Denda" required>
            </div>
            <div class="form-group">
                <label for="jumlah_buku" class="form-label">Jumlah Buku</label>
                <input type="number" id="jumlah_buku" name="jumlah_buku" class="form-control"
                    placeholder="Masukkan Jumlah Buku" required>
            </div>
            <div class="form-group">
                <label for="tanggal_denda" class="form-label">Tanggal Denda</label>
                <input type="date" id="tanggal_denda" name="tanggal_denda" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>

    <div class="footer mt-4">
        &copy; 2024 Sistem Perpustakaan. All rights reserved.
    </div>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>