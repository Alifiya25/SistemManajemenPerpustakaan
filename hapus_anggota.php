<?php
session_start();
include("connect.php");

if (!isset($_GET['id_anggota'])) {
    echo "<script>
    alert('ID anggota tidak ditemukan.');
    window.location.href = 'anggota.php';
    </script>";
    exit();
}

$id_anggota = $_GET['id_anggota'];

// Cek tunggakan anggota
$query_tunggakan = "SELECT IFNULL(SUM(d.TOTAL_DENDA), 0) AS TUNGGAKAN 
                    FROM peminjaman p 
                    LEFT JOIN denda d ON p.ID_PEMINJAMAN = d.ID_PEMINJAMAN 
                    WHERE p.ID_ANGGOTA = ?";
$stmt = $conn->prepare($query_tunggakan);
$stmt->bind_param('i', $id_anggota);
$stmt->execute();
$result = $stmt->get_result();
$tunggakan = $result->fetch_assoc()['TUNGGAKAN'] ?? 0;

if ($tunggakan > 0) {
    echo "<script>
    alert('Anggota tidak bisa dihapus karena memiliki tunggakan.');
    window.location.href = 'anggota.php';
    </script>";
    exit();
}

// Hapus anggota jika tidak ada tunggakan
$query_hapus = "DELETE FROM anggota WHERE ID_ANGGOTA = ?";
$stmt = $conn->prepare($query_hapus);
$stmt->bind_param('i', $id_anggota);

if ($stmt->execute()) {
    echo "<script>
    alert('Anggota berhasil dihapus.');
    window.location.href = 'anggota.php';
    </script>";
} else {
    echo "<script>
    alert('Terjadi kesalahan saat menghapus anggota.');
    window.location.href = 'anggota.php';
    </script>";
}
?>