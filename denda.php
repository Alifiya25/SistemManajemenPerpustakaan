<?php
include 'connect.php'; // Koneksi database

// Fungsi untuk mengambil data denda
$sql = "SELECT denda.ID_DENDA, peminjaman.ID_ANGGOTA, denda.ID_PEMINJAMAN, denda.ID_PUSTAKAWAN, 
        denda.ID_BUKU, denda.TOTAL_DENDA, denda.JUMLAH_BUKU 
        FROM denda
        JOIN peminjaman ON denda.ID_PEMINJAMAN = peminjaman.ID_PEMINJAMAN";
$result = $conn->query($sql);

/// Jika ada parameter 'delete', hapus data
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM denda WHERE ID_DENDA = ?");
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
    header("Location: denda.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Denda Buku</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #e3f2fd, #dcedc8);
        min-height: 100vh;
        background-color: #e8f5e9;
        color: #2e7d32;
        display: flex;
        flex-direction: column;
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
        padding: 30px 20px;
        text-align: center;
        margin-bottom: 0px;
        font-size: 28px;
        font-weight: bold;
    }

    .stats {
        display: flex;
        justify-content: center;
        margin-bottom: 1px;
        padding: 1px;
        border-radius: 5px;
        position: relative;
    }

    .stats>div {
        padding: 10px;
        border-radius: 5px;
        color: white;
        /* Warna teks putih untuk kontras */
        margin: 0 10px;
        /* Memberikan jarak antar elemen */
        background-color: #4caf50;
    }

    .search-bar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
        margin-top: 20px;
    }

    .search-bar input {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-right: 10px;
    }

    .search-bar button {
        background: #4caf50;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 5px;
        cursor: pointer;
    }

    .search-bar button:hover {
        background: #45a049;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        height: 30%;
    }

    .table-container {
        flex: 1;
        overflow-x: auto;
        overflow-y: scroll;
        max-height: calc(50vh - 50px);
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px #4caf50;
        padding: 8px;
        padding-bottom: 100px;
        margin-bottom: 100px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        margin-top: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th,
    table td {
        border: 1px solid #81c784;
        text-align: center;
        padding: 10px;
    }

    table th {
        background: #4caf50;
        color: white;
        text-align: center;
        position: sticky;
        top: 0;
    }

    table td {
        background-color: #e8f5e9;
    }

    table tr:nth-child(even) td {
        background-color: #c8e6c9;
    }

    table tr:hover td {
        background-color: #c5e1a5;
    }

    .actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 15px;
        position: fixed;
        bottom: 60px;
        left: 0;
        right: 0;
        z-index: 1000;
    }

    button {
        background-color: #4caf50;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        margin: 0 10px;
    }

    button:hover {
        background-color: #338a3e;
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
    }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="title">SMARTLIB</div>
        <div class="nav-buttons">
            <a href="menu_utama.php"><i class="fas fa-sign-out-alt"></i> Menu Utama</a>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="welcome-container">
        DATA DENDA
    </div>

    <!-- Stats -->
    <div class="stats">
        <div>Denda: <span id="total_denda">0</span></div>
        <div>Denda Hari Ini: <span id="belum_dibayar">0</span></div>
        <div>Total Denda: <span id="total_denda">0</span></div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="get" action="denda.php">
            <input type="text" name="search" placeholder="Cari data...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                        <th>ID Denda</th>
                        <th>ID Peminjaman</th>
                        <th>ID Anggota</th>
                        <th>Total Denda</th>
                        <th>Jumlah Buku</th>
                        <th>Tanggal Denda</th>
                        <th>Pustakawan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['ID_DENDA']); ?></td>
                        <td><?php echo htmlspecialchars($row['ID_PEMINJAMAN']); ?></td>
                        <td><?php echo htmlspecialchars($row['ID_ANGGOTA']); ?></td>
                        <td><?php echo htmlspecialchars($row['ID_PUSTAKAWAN']); ?></td>
                        <td><?php echo htmlspecialchars($row['ID_BUKU']); ?></td>
                        <td><?php echo htmlspecialchars($row['TOTAL_DENDA']); ?></td>
                        <td><?php echo htmlspecialchars($row['JUMLAH_BUKU']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8">Tidak ada data denda.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="actions">
        <a href="hapus_denda.php" class="btn btn-danger">Hapus</a>
        <a href="tambah_denda.php" class="btn btn-success">Tambah</a>
        <a href="#" onclick="editSelected()" class="btn btn-warning">Edit</a>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2024 Sistem Perpustakaan. All rights reserved.
    </div>
</body>

</html>