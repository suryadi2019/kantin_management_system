<?php
require_once 'db_connect.php';
session_start(); // Diperlukan untuk mencatat log

// Validasi jika ID transaksi ada di URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id_transaksi = $_GET['id'];

  // Update status pesanan menjadi 'selesai'
  $sql = "UPDATE transaction SET status_pesanan = 'selesai' WHERE id_transaction = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, 'i', $id_transaksi);

  if (mysqli_stmt_execute($stmt)) {
    // Catat log aktivitas (teks diubah)
    createLog($conn, "Menandai transaksi #" . $id_transaksi . " telah dilayani (status: selesai).");

    // Jika berhasil, kembalikan ke halaman daftar pesanan
    header('Location: pesanan_pending.php?status=sukses');
    exit();
  } else {
    echo "Gagal memperbarui status pesanan.";
  }
} else {
  // Jika tidak ada ID, kembali ke halaman pesanan pending
  header('Location: pesanan_pending.php');
  exit();
}
