<?php
// Informasi koneksi database
$host = "localhost";      
$username = "root";       
$password = "";           
$database = "perpustakaan"; 

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $database);

// Periksa apakah koneksi berhasil
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Aktifkan sesi jika belum diinisialisasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>