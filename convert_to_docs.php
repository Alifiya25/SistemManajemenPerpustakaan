<?php
session_start();
include("connect.php");

// Memeriksa apakah pengguna memiliki akses untuk Pustakawan Jaga I atau IV
if (!isset($_SESSION['ID_ROLE']) || !in_array($_SESSION['ID_ROLE'], [1, 4])) {
    echo "<script>
    alert('Akses tidak diizinkan untuk Anda.');
    window.location.href = 'menu_utama.php';
    </script>";
    exit();
}

// Mengambil data anggota
$query_anggota = "SELECT a.*, IFNULL(SUM(d.TOTAL_DENDA), 0) AS TUNGGAKAN
FROM anggota a
LEFT JOIN peminjaman p ON a.ID_ANGGOTA = p.ID_ANGGOTA
LEFT JOIN denda d ON p.ID_PEMINJAMAN = d.ID_PEMINJAMAN
GROUP BY a.ID_ANGGOTA";
$result_anggota = $conn->query($query_anggota);

// Memuat PHPWord
require_once 'vendor/autoload.php';
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// Membuat objek PHPWord
$phpWord = new PhpWord();

// Menambahkan bagian untuk daftar anggota
$section = $phpWord->addSection();
$section->addTitle('Daftar Anggota', 1);

// Membuat header tabel
$table = $section->addTable();
$table->addRow();
$table->addCell(2000)->addText('ID Anggota');
$table->addCell(4000)->addText('Nama');
$table->addCell(4000)->addText('Tanggal Lahir');
$table->addCell(4000)->addText('Tanggal Join');
$table->addCell(4000)->addText('Alamat');
$table->addCell(4000)->addText('No. Telepon');
$table->addCell(4000)->addText('Tunggakan');

// Menambahkan data anggota ke dalam tabel
while ($row = $result_anggota->fetch_assoc()) {
    $table->addRow();
    $table->addCell(2000)->addText($row['ID_ANGGOTA']);
    $table->addCell(4000)->addText($row['NAMA']);
    $table->addCell(4000)->addText($row['TANGGAL_LAHIR']);
    $table->addCell(4000)->addText($row['TANGGAL_JOIN']);
    $table->addCell(4000)->addText($row['ALAMAT']);
    $table->addCell(4000)->addText($row['KONTAK']);
    $table->addCell(4000)->addText('Rp.' . number_format($row['TUNGGAKAN'], 0, ',', '.'));
}

// Menyimpan file Word
$file_name = 'Daftar_Anggota_' . date('Ymd_His') . '.docx';
$phpWord->save($file_name, 'Word2007');

// Mengunduh file Word
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=$file_name");
header("Content-Length: " . filesize($file_name));
readfile($file_name);

// Hapus file setelah diunduh
unlink($file_name);
exit();
?>