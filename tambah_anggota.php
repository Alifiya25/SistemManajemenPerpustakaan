<?php
// Menghubungkan ke database
include 'connect.php';

// Mengambil ID_ANGGOTA terakhir untuk menghitung ID berikutnya
$query = "SELECT ID_ANGGOTA FROM anggota ORDER BY ID_ANGGOTA DESC LIMIT 1";
$result = $conn->query($query);

$next_id = '201001'; // Default ID pertama jika tabel kosong

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Menghitung ID_ANGGOTA berikutnya dengan menambah 1 pada ID terakhir
    $last_id = $row['ID_ANGGOTA'];
    $next_id = str_pad($last_id + 1, 6, '0', STR_PAD_LEFT); // Memformat ID berikutnya (misalnya 201002)
}

// Mengecek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mendapatkan data dari form
    $nama_lengkap = $_POST['nama_lengkap'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $tanggal_join = $_POST['tanggal_join'];
    $alamat = $_POST['alamat'];
    $kontak = $_POST['kontak'];

    // Query untuk memasukkan data anggota baru
    $sql = "INSERT INTO anggota (ID_ANGGOTA, NAMA, ALAMAT, TANGGAL_JOIN, TANGGAL_LAHIR, KONTAK) 
            VALUES ('$next_id', '$nama_lengkap', '$alamat', '$tanggal_join', '$tanggal_lahir', '$kontak')";

    // Eksekusi query
    if ($conn->query($sql) === TRUE) {
        // Setelah data berhasil dimasukkan, redirect ke halaman yang sama dengan status success
        header("Location: anggota.php?status=success");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error; // Menampilkan pesan error jika query gagal
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tambah Anggota</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<style>

</style>

<body>
    <!-- Header -->
    <div class="header">
        <div class="title">SMARTLIB</div>
        <div class="nav-buttons">
            <a href="anggota.php"><i class="fas fa-sign-out-alt"></i> Back</a>
        </div>
    </div>

    <div class="welcome-container-anggota">
        <div class="d-flex justify-content-between mb-4">
            <!-- Judul Form -->
            <h2>Form Tambah Anggota</h2>
        </div>

        <form action="tambah_anggota.php" method="POST">
            <div class="mb-3 row">
                <label for="id_anggota" class="col-sm-2 col-form-label">ID Anggota</label>
                <div class="col-sm-10">
                    <input type="text" id="id_anggota" name="id_anggota" class="form-control"
                        value="<?php echo $next_id; ?>" readonly>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="nama_lengkap" class="col-sm-2 col-form-label">Nama Lengkap</label>
                <div class="col-sm-10">
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control"
                        placeholder="Masukkan Nama Lengkap" required>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="tanggal_lahir" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                <div class="col-sm-10">
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control" required>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="tanggal_join" class="col-sm-2 col-form-label">Tanggal Join</label>
                <div class="col-sm-10">
                    <input type="date" id="tanggal_join" name="tanggal_join" class="form-control" required>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                <div class="col-sm-10">
                    <textarea id="alamat" name="alamat" class="form-control" rows="3" placeholder="Masukkan Alamat"
                        required></textarea>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="kontak" class="col-sm-2 col-form-label">Kontak</label>
                <div class="col-sm-10">
                    <input type="text" id="kontak" name="kontak" class="form-control"
                        placeholder="Masukkan Nomor Kontak" required>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="button">OK</button>
            </div>
        </form>
        <!-- Footer -->
        <div class="footer-anggota">
            &copy; 2024 Sistem Perpustakaan. All rights reserved.
        </div>

        <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<style>