<!-- FILE: pengembalian.php -->
<?php
include 'connect.php'; // Koneksi database

// Memeriksa apakah pengguna memiliki akses untuk Pustakawan Jaga I atau IV
if (!isset($_SESSION['ID_ROLE']) || !in_array($_SESSION['ID_ROLE'], [1, 4])) {
    echo "<script>
    alert('Akses tidak diizinkan untuk Anda.');
    window.location.href = 'menu_utama.php';
    </script>";
    exit();
}

// Menangani input pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fungsi untuk mengambil data pengembalian
$sql = "SELECT ID_PENGEMBALIAN, ID_PEMINJAMAN, ID_ANGGOTA, JUMLAH_BUKU, TANGGAL_PINJAM, TANGGAL_KEMBALI, KETERANGAN FROM pengembalian";
$result = $conn->query($sql);

// Jika parameter search diisi, tambahkan klausa WHERE
if (!empty($search)) {
    $sql .= " WHERE ID_PENGEMBALIAN LIKE ?";
}

// Persiapkan query
$stmt = $conn->prepare($sql);

// Jika ada input search, bind parameter
if (!empty($search)) {
    $search_param = "%" . $search . "%";
    $stmt->bind_param("s", $search_param);
}

$stmt->execute();
$result = $stmt->get_result();

// Menghitung statistik pengembalian
$date_today = date('Y-m-d');

// Pengembalian total
$total_query = "SELECT COUNT(*) AS total FROM pengembalian";
$total_result = $conn->query($total_query);
$total_pengembalian = $total_result->fetch_assoc()['total'];

// Pengembalian terlambat
$terlambat_query = "SELECT COUNT(*) AS terlambat FROM pengembalian WHERE TANGGAL_KEMBALI < CURDATE()";
$terlambat_result = $conn->query($terlambat_query);
$total_terlambat = $terlambat_result->fetch_assoc()['terlambat'];

// Query untuk menghitung pengembalian terlambat
$terlambat_query = "SELECT COUNT(*) AS terlambat FROM pengembalian WHERE KETERANGAN = 'Terlambat'";
$terlambat_result = $conn->query($terlambat_query);

// Mengambil hasil query
$total_terlambat = $terlambat_result->fetch_assoc()['terlambat'];

// Pengembalian hari ini
$hari_ini_query = "SELECT COUNT(*) AS hari_ini FROM pengembalian WHERE TANGGAL_KEMBALI = CURDATE()";
$hari_ini_result = $conn->query($hari_ini_query);
$total_hari_ini = $hari_ini_result->fetch_assoc()['hari_ini'];

// Jika ada parameter 'delete', hapus data
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM pengembalian WHERE ID_PENGEMBALIAN = ?");
    $stmt->bind_param("s", $id_to_delete);
    $stmt->execute();
    header("Location: pengembalian.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengembalian Buku</title>
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
        .stats > div {
            padding: 10px;
            border-radius: 5px;
            color: white; /* Warna teks putih untuk kontras */
            margin: 0 10px; /* Memberikan jarak antar elemen */
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
            height: 10%;
        }
        .table-container {
            flex: 1;
            overflow-x: auto;
            overflow-y: scroll;
            max-height: calc(40vh - 50px);
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px #4caf50;
            padding: 8px;
            padding-bottom: 100px;
            margin-bottom: 100px;
            max-width: 1200px;
            margin-left: auto;   /* Mengatur margin kiri otomatis */
            margin-right: auto;  /* Mengatur margin kanan otomatis */
            margin-top: 10px; /* Memberikan jarak atas agar tidak terlalu dekat dengan Total Anggota */
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
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
            background-color: #e8f5e9;  /* Latar belakang sel data */
        }
        table tr:nth-child(even) td {
            background-color: #c8e6c9;  /* Latar belakang alternatif untuk baris genap */
        }
        table tr:hover td {
            background-color: #c5e1a5;  /* Latar belakang ketika baris di-hover */
        }
        .actions {
            display: flex;
            justify-content: space-between; /* Membuat ruang antara elemen aksi */
            gap: 10px; /* Jarak antar tombol */
            padding: 10px 15px;
            position: fixed; /* Tetap di bawah layar */
            bottom: 60px; /* Beri jarak antara tombol dan footer */
            left: 0;
            right: 0;
            z-index: 1000; /* Agar tombol tetap di atas konten lainnya */
        }
        .action-left,
        .action-center,
        .action-right {
            display: inline-block;
        }
        .action-center {
            margin: 0 auto;/* Memusatkan elemen ini */
        }
        button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
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
            z-index: 100;/* Pastikan footer berada di atas konten lain */
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

             <!-- Welcome Message -->
            <div class="welcome-container">
                    DATA PENGEMBALIAN 
            </div>

            <!-- Stats -->
            <div class="stats">
                <div>Pengembalian: <span id="total_pengembalian"><?= $total_pengembalian ?></span></div>
                <div>Terlambat: <span id="total_terlambat"><?= $total_terlambat ?></span></div>
                <div>Dikembalikan Hari Ini: <span id="dikembalikan_hari_ini"><?= $total_hari_ini ?></span></div>
            </div>

            <!-- Search Bar -->
            <div class="search-bar">
                <form method="get" action="pengembalian.php">
                <input type="text" name="search" placeholder="Cari data..." value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
                </form>
            </div>

            <!-- Table -->
            <div class="table-wrapper">
                <div class="table-container">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                                <th>ID Pengembalian</th>
                                <th>ID Peminjaman</th>
                                <th>ID Anggota</th>
                                <th>Jumlah Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><input type="checkbox" name="selected[]" value="<?= $row['ID_PENGEMBALIAN'] ?>"></td>
                                    <td><?= htmlspecialchars($row['ID_PENGEMBALIAN'], ENT_QUOTES) ?></td>
                                    <td><?= htmlspecialchars($row['ID_PEMINJAMAN'], ENT_QUOTES) ?></td>
                                    <td><?= htmlspecialchars($row['ID_ANGGOTA'], ENT_QUOTES) ?></td>
                                    <td><?= htmlspecialchars($row['JUMLAH_BUKU'], ENT_QUOTES) ?></td>
                                    <td><?= htmlspecialchars($row['TANGGAL_PINJAM'], ENT_QUOTES) ?></td>
                                    <td><?= htmlspecialchars($row['TANGGAL_KEMBALI'], ENT_QUOTES) ?></td>
                                    <td><?= htmlspecialchars($row['KETERANGAN'], ENT_QUOTES) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data pengembalian.</td>
                                </tr>
                        <?php endif; ?>
                </tbody>
                </table>
                </div>
                    <div class="actions">
                        <!-- Tombol Hapus -->
                        <a href="hapus_pengembalian.php" class="btn btn-danger">Hapus</a>
                        <a href="tambah_pengembalian.php" class="btn btn-success">Tambah</a>
                        <a href="#" onclick="editSelected()" class="btn btn-warning">Edit</a>
                    </div>
                    <div class="footer">
                        &copy; 2024 Sistem Perpustakaan. All rights reserved.
                    </div>
        </div>
    </body>
</html>
