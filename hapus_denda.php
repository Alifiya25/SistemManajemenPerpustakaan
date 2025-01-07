<?php
include("connect.php");

if (isset($_GET['id_denda'])) {
    $id_denda = $_GET['id_denda'];
    $id_denda_array = array_map('intval', explode(',', $id_denda));

    // Cek apakah denda terkait dengan data lain
    $query_cek = "SELECT COUNT(*) AS jumlah FROM denda WHERE ID_DENDA IN (" . implode(",", $id_denda_array) . ")";
    $stmt_cek = $conn->prepare($query_cek);
    $stmt_cek->execute();
    $result_cek = $stmt_cek->get_result();
    $jumlah_denda = $result_cek->fetch_assoc()['jumlah'];

    if ($jumlah_denda > 0) {
        // Hapus denda
        $query_hapus = "DELETE FROM denda WHERE ID_DENDA IN (" . implode(",", $id_denda_array) . ")";
        $stmt_hapus = $conn->prepare($query_hapus);
        if ($stmt_hapus->execute()) {
            echo "<script>alert('Denda berhasil dihapus.'); window.location.href = 'denda.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus denda.'); window.location.href = 'denda.php';</script>";
        }
    } else {
        echo "<script>alert('Tidak ada denda yang ditemukan untuk dihapus.'); window.location.href = 'denda.php';</script>";
    }
} else {
    echo "<script>alert('Tidak ada ID denda yang dipilih.'); window.location.href = 'denda.php';</script>";
}
?>