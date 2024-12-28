<?php
include("connect.php");

$id_anggota = $_GET['id_anggota'] ?? 0;

// Query untuk cek total tunggakan
$query = "SELECT IFNULL(SUM(d.TOTAL_DENDA), 0) AS tunggakan
          FROM peminjaman p
          LEFT JOIN denda d ON p.ID_PEMINJAMAN = d.ID_PEMINJAMAN
          WHERE p.ID_ANGGOTA = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_anggota);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>